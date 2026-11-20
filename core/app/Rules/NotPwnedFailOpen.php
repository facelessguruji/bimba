<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

/**
 * Breach check with fail-open semantics: the haveibeenpwned API is an
 * external dependency — when it is unreachable, validation PASSES (with a
 * log entry) instead of locking every member out of registration on an
 * offline host. A definitive "pwned" answer still rejects the password.
 */
class NotPwnedFailOpen implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $hash    = strtoupper(sha1($value));
            $prefix  = substr($hash, 0, 5);
            $suffix  = substr($hash, 5);
            $response = Http::timeout(3)
                ->get('https://api.pwnedpasswords.com/range/' . $prefix);
            if (! $response->successful()) {
                report(new \Exception('pwnedpasswords API unavailable — breach check skipped (fail-open)'));
                return true;
            }
            return ! collect(explode("\n", $response->body()))
                ->contains(fn ($line) => str_starts_with(trim($line), $suffix . ':'));
        } catch (\Throwable $e) {
            report($e);
            return true;
        }
    }

    public function message()
    {
        return 'This password has appeared in a data breach. Please choose a different one.';
    }
}
