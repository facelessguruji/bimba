<?php

namespace App\Lib;

use App\Models\AiLog;
use GuzzleHttp\Client;

class AiService
{
    /**
     * AI is available only when the admin enabled it AND provided a key.
     */
    public static function enabled($feature = null)
    {
        if (!gs('ai_enable') || !gs('ai_api_key')) {
            return false;
        }
        if ($feature) {
            $features = self::features();
            if (isset($features[$feature]) && !$features[$feature]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Per-feature toggles from gs('ai_features') JSON.
     */
    public static function features()
    {
        $features = json_decode(gs('ai_features') ?: '{}', true) ?: [];
        return array_merge([
            'listing_writer' => true,
            'seo'            => true,
            'lead_match'     => true,
            'review_summary' => true,
        ], $features);
    }

    /**
     * Monthly usage cap check (0 = unlimited).
     */
    public static function underCap()
    {
        $cap = (int) gs('ai_monthly_cap');
        if ($cap <= 0) {
            return true;
        }
        $used = AiLog::where('created_at', '>=', now()->startOfMonth())->sum('tokens');
        return $used < $cap;
    }

    /**
     * Thin OpenAI-compatible chat completion call. Returns the message
     * content or null on any failure  -  AI must never break the platform.
     */
    public static function chat($messages, $feature = 'general', $userId = null, $maxTokens = 700)
    {
        if (!self::enabled()) {
            return null;
        }
        try {
            $client = new Client([
                'base_uri' => rtrim(gs('ai_base_url') ?: 'https://api.openai.com/v1', '/') . '/',
                'timeout'  => 30,
            ]);

            $response = $client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . gs('ai_api_key'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'      => gs('ai_model') ?: 'gpt-4o-mini',
                    'messages'   => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature'=> 0.7,
                ],
            ]);

            $body     = json_decode((string) $response->getBody(), true);
            $content  = $body['choices'][0]['message']['content'] ?? null;
            $tokens   = $body['usage']['total_tokens'] ?? 0;

            try {
                $log          = new AiLog();
                $log->user_id = $userId;
                $log->feature = $feature;
                $log->tokens  = (int) $tokens;
                $log->save();
            } catch (\Throwable $e) {
                // logging must never break the flow
            }

            return $content;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Extract JSON from a model response (model may wrap it in prose/fences).
     */
    public static function extractJson($text)
    {
        if (!$text) {
            return null;
        }
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return null;
    }
}
