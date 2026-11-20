<?php

namespace App\Http\Controllers;

use App\Lib\Affiliate;
use Carbon\Carbon;
use App\Models\CronJob;
use App\Lib\CurlRequest;
use App\Constants\Status;
use App\Models\CronJobLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    public function cron()
    {
        // guard reads the cached config (env() returns null under config:cache).
        // trusted paths: CLI/artisan from the server console, an authenticated
        // admin session (the panel's Run Manually button), or a web request
        // carrying ?key=<secret> when a secret is configured. Every other web
        // request is rejected with 403.
        $secret    = config('app.cron_secret');
        $isAdminRun = auth('admin')->check();
        if (!$isAdminRun && !app()->runningInConsole()) {
            if ($secret) {
                // hash_equals: constant-time comparison, not leaking the secret
                // byte by byte through early-exit timing
                if (!hash_equals((string) $secret, (string) request()->query('key'))) {
                    abort(403);
                }
            } else {
                abort(403);
            }
        }

        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            // a missing/deleted schedule row must not crash the whole run
            // (one broken row used to 500 the endpoint and skip later jobs)
            $interval = $cron->schedule?->interval ?? 3600;
            $cron->next_run = now()->addSeconds($interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Plan expiry sweep: plan rows past their expiry are marked disabled so
     * listing visibility and the withdrawal gate react to real state. The
     * referrers of expired members get their direct_active counter
     * recomputed so rank thresholds always reflect live reality.
     */
    public function planExpirySweep()
    {
        $expiredUsers = DB::table('user_plans')
            ->join('users', 'users.id', '=', 'user_plans.user_id')
            ->where('user_plans.status', Status::ENABLE)
            ->whereDate('user_plans.expired_on', '<=', now())
            ->where('user_plans.plan_price', '>', 0)
            ->pluck('users.ref_by')
            ->unique()
            ->filter();

        DB::table('user_plans')
            ->where('status', Status::ENABLE)
            ->whereDate('expired_on', '<=', now())
            ->update(['status' => Status::DISABLE, 'updated_at' => now()]);

        foreach ($expiredUsers as $referrerId) {
            // atomic single-statement recompute  -  a purchase committing its
            // direct_active increment mid-sweep can never be overwritten by a
            // stale PHP-side count (read-then-write race)
            DB::table('user_extras')->where('user_id', $referrerId)->update([
                'direct_active' => DB::raw('(SELECT COUNT(DISTINCT up.user_id) FROM user_plans up
                    JOIN users u ON u.id = up.user_id
                    WHERE u.ref_by = ' . (int) $referrerId . '
                        AND up.status = ' . (int) Status::ENABLE . "
                        AND up.expired_on > '" . now()->toDateString() . " 23:59:59'
                        AND up.plan_price > 0)"),
            ]);
        }
    }

    /**
     * Rank re-check: re-evaluate every member against the dual threshold.
     */
    public function cronRank()
    {
        $engine = new Affiliate();
        User::active()->with('userExtra')->chunkById(200, function ($users) use ($engine) {
            foreach ($users as $user) {
                if (!$user->userExtra) {
                    // lazily create the counters row so rank 1 (threshold 0)
                    // can be applied to members who never referred anyone
                    (new \App\Lib\Affiliate())->userExtra($user);
                    $user->load('userExtra');
                }
                $engine->updateRankState($user);
            }
        });
    }
}
