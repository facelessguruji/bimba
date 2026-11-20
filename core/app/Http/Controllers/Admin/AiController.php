<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\AiService;
use App\Models\AiLog;
use App\Models\GeneralSetting;
use App\Models\Lead;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function settings()
    {
        $pageTitle = 'AI Settings';
        return view('admin.ai.settings', compact('pageTitle'));
    }

    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'ai_base_url'   => 'required|url',
            'ai_model'      => 'required|max:60',
            'ai_api_key'    => 'nullable|max:255',
            'ai_monthly_cap' => 'required|integer|min:0',
        ]);

        $general = GeneralSetting::first();
        $general->ai_enable      = $request->boolean('ai_enable') ? 1 : 0;
        $general->ai_base_url    = $request->ai_base_url;
        $general->ai_model       = $request->ai_model;
        $general->ai_monthly_cap = (int) $request->ai_monthly_cap;
        if ($request->filled('ai_api_key')) {
            $general->ai_api_key = $request->ai_api_key;
        }

        $features = $request->ai_features ?? [];
        $general->ai_features = json_encode([
            'listing_writer' => (bool) ($features['listing_writer'] ?? false),
            'seo'            => (bool) ($features['seo'] ?? false),
            'lead_match'     => (bool) ($features['lead_match'] ?? false),
            'review_summary' => (bool) ($features['review_summary'] ?? false),
        ]);
        $general->save();

        $notify[] = ['success', 'AI settings updated successfully'];
        return back()->withNotify($notify);
    }

    public function logs()
    {
        $pageTitle = 'AI Usage Logs';
        $logs      = AiLog::with('user')->searchable(['feature'])->orderBy('id', 'desc')->paginate(getPaginate());
        $monthUsage = AiLog::where('created_at', '>=', now()->startOfMonth())->sum('tokens');
        return view('admin.ai.logs', compact('pageTitle', 'logs', 'monthUsage'));
    }

    public function leads()
    {
        $pageTitle = 'Leads';

        // the admin sees every lead in the system and can slice by owner
        // or pipeline stage
        $query = Lead::with('listing', 'owner')
            ->searchable(['name', 'email', 'phone', 'listing:title'])
            ->filter(['status'])
            ->orderBy('id', 'desc');

        $leads   = $query->paginate(getPaginate());
        $totals  = [
            'all'       => Lead::count(),
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'closed'    => Lead::where('status', 'closed')->count(),
        ];

        return view('admin.ai.leads', compact('pageTitle', 'leads', 'totals'));
    }

    /**
     * Export leads to CSV (opens directly in Excel). The same filters as
     * the listing page apply, so an admin can export exactly the slice
     * they are looking at.
     */
    public function leadExport(Request $request)
    {
        $fileName = 'leadxgpt-leads-' . now()->format('Y-m-d-His') . '.csv';

        $leads = Lead::with('listing', 'owner')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('id', 'desc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($leads) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens accented names correctly
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Listing', 'Owner', 'Enquirer Name', 'Email', 'Phone', 'Message', 'Status', 'Source', 'Date']);
            foreach ($leads as $lead) {
                fputcsv($out, [
                    $lead->id,
                    csvSafe($lead->listing?->title),
                    csvSafe($lead->owner?->username ?: $lead->owner?->email),
                    csvSafe($lead->name),
                    csvSafe($lead->email),
                    csvSafe($lead->phone),
                    csvSafe($lead->message),
                    $lead->status,
                    $lead->source,
                    optional($lead->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    /**
     * Import leads from a CSV (Excel-compatible). Required headers:
     * listing_id, name, email. Optional: phone, message. Every imported
     * lead is routed to the listing's owner automatically and marked
     * with source "import".
     */
    public function leadImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $handle = fopen($request->file('import_file')->getRealPath(), 'r');
        if (!$handle) {
            $notify[] = ['error', 'Could not read the uploaded file'];
            return back()->withNotify($notify);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $notify[] = ['error', 'The file is empty'];
            return back()->withNotify($notify);
        }

        // tolerate "Listing Id", "listing id", "listing_id" etc.
        $header = array_map(fn ($col) => strtolower(trim(preg_replace('/[^a-z0-9_ ]/i', '', (string) $col))), $header);
        $column = fn (array $row, string $key) => $row[array_search($key, $header)] ?? null;

        $inserted = 0;
        $skipped  = 0;
        $now      = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (!count(array_filter($row))) {
                continue;
            }

            $listing = Listing::find((int) $column($row, 'listing_id'));
            $name    = trim((string) $column($row, 'name'));
            $email   = trim((string) $column($row, 'email'));

            if (!$listing || $name === '' || $email === '') {
                $skipped++;
                continue;
            }

            $lead             = new Lead();
            $lead->listing_id = $listing->id;
            $lead->user_id    = $listing->user_id;
            $lead->name       = $name;
            $lead->email      = $email;
            $lead->phone      = trim((string) $column($row, 'phone'));
            $lead->message    = trim((string) $column($row, 'message'));
            $lead->status     = 'new';
            $lead->source     = 'import';
            $lead->created_at = $now;
            $lead->updated_at = $now;
            $lead->save();
            $inserted++;
        }

        fclose($handle);

        $notify[] = ['success', "$inserted lead(s) imported, $skipped skipped (rows need a valid listing_id, name and email)"];
        return back()->withNotify($notify);
    }


    public function leadStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:new,contacted,closed']);
        $lead         = Lead::findOrFail($id);
        $lead->status = $request->status;
        $lead->save();
        $notify[] = ['success', 'Lead status updated'];
        return back()->withNotify($notify);
    }

    public function leadDetails($id)
    {
        $lead      = Lead::with('listing', 'owner')->findOrFail($id);
        $pageTitle = 'Lead Details';

        // match_result is either a JSON array of scored listings (AI on)
        // or a bare keyword-match score - only an array renders as a list
        $decoded = $lead->match_result ? json_decode($lead->match_result, true) : [];
        $matches = is_array($decoded) ? $decoded : [];

        return view('admin.ai.lead_details', compact('pageTitle', 'lead', 'matches'));
    }

    /**
     * AI SEO helper  -  generate meta title/description for any subject.
     */
    public function seo(Request $request)
    {
        $request->validate(['subject' => 'required|string|max:2000']);
        if (!AiService::enabled('seo')) {
            return response()->json(['error' => true, 'message' => 'AI SEO helper is disabled']);
        }
        $content = AiService::chat([
            ['role' => 'system', 'content' => 'You are an SEO expert. Reply ONLY with JSON {"title": "max 60 chars", "description": "max 160 chars"}.'],
            ['role' => 'user', 'content' => 'Generate SEO meta for: ' . $request->subject],
        ], 'seo', auth()->guard('admin')->id(), 200);
        $data = AiService::extractJson($content);
        if (!$data) {
            return response()->json(['error' => true, 'message' => 'AI did not return usable content']);
        }
        return response()->json(['error' => false, 'data' => $data]);
    }
}
