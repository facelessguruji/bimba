<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\CronSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CronConfigurationController extends Controller
{
    public function cronJobs()
    {
        $pageTitle = 'Cron Jobs';
        $crons     = CronJob::with('schedule', 'logs')->get();
        $schedules = CronSchedule::active()->orderBy('interval')->get();
        return view('admin.cron.index', compact('pageTitle', 'crons', 'schedules'));
    }

    public function flushLog($id)
    {
        CronJobLog::where('cron_job_id', $id)->delete();
        $notify[] = ['success', 'Cron job logs cleared'];
        return back()->withNotify($notify);
    }

    public function schedule()
    {
        $pageTitle = 'Cron Schedules';
        $schedules = CronSchedule::orderBy('interval')->paginate(getPaginate());
        return view('admin.cron.schedule', compact('pageTitle', 'schedules'));
    }

    public function cronJobPause($id)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->is_running = $cronJob->is_running ? Status::DISABLE : Status::ENABLE;
        $cronJob->save();
        $notify[] = ['success', 'Cron job status changed successfully'];
        return back()->withNotify($notify);
    }

    public function cronJobStore(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'next_run'         => 'required',
            'cron_schedule_id' => 'required|integer',
            'url'              => 'required|url',
        ]);

        $cronJob                   = new CronJob();
        $cronJob->name             = $request->name;
        $cronJob->alias            = titleToKey($request->name);
        $cronJob->next_run         = Carbon::parse($request->next_run)->toDateTimeString();
        $cronJob->cron_schedule_id = $request->cron_schedule_id;
        $cronJob->url              = $request->url;
        $cronJob->is_default       = Status::NO;
        $cronJob->save();

        $notify[] = ['success', 'Cron job added successfully'];
        return to_route('admin.cron.index')->withNotify($notify);
    }

    public function cronJobUpdate(Request $request)
    {
        $request->validate([
            'id'               => 'required|integer',
            'name'             => 'required',
            'next_run'         => 'required',
            'cron_schedule_id' => 'required|integer',
        ]);

        $cronJob       = CronJob::findOrFail($request->id);
        $cronJob->name = $request->name;

        if (!$cronJob->is_default) {
            $request->validate(['url' => 'required|url']);
            $cronJob->url   = $request->url;
            $cronJob->alias = titleToKey($request->name);
        }

        $cronJob->next_run         = Carbon::parse($request->next_run)->toDateTimeString();
        $cronJob->cron_schedule_id = $request->cron_schedule_id;
        $cronJob->save();

        $notify[] = ['success', 'Cron job updated successfully'];
        return to_route('admin.cron.index')->withNotify($notify);
    }

    public function cronJobDelete(Request $request)
    {
        if ($request->id == 0) {
            $notify[] = ['error', 'Default cron job can\'t be deleted'];
            return back()->withNotify($notify);
        }
        CronJob::where('id', $request->id)->where('is_default', Status::NO)->firstOrFail()->delete();
        $notify[] = ['success', 'Cron job deleted successfully'];
        return back()->withNotify($notify);
    }

    public function scheduleStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:255',
            'interval' => 'required|integer|gt:0',
        ]);

        $schedule         = new CronSchedule();
        $schedule->name   = $request->name;
        $schedule->interval = $request->interval;
        $schedule->save();

        $notify[] = ['success', 'Cron schedule added successfully'];
        return back()->withNotify($notify);
    }

    public function scheduleUpdate(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|max:255',
            'interval' => 'required|integer|gt:0',
        ]);

        $schedule           = CronSchedule::findOrFail($id);
        $schedule->name     = $request->name;
        $schedule->interval = $request->interval;
        $schedule->save();

        $notify[] = ['success', 'Cron schedule updated successfully'];
        return back()->withNotify($notify);
    }

    public function scheduleStatus($id)
    {
        return CronSchedule::changeStatus($id);
    }

    public function scheduleDelete($id)
    {
        $schedule = CronSchedule::findOrFail($id);

        try {
            CronJob::where('cron_schedule_id', $schedule->id)->update(['cron_schedule_id' => 1]);
            $schedule->delete();
        } catch (\Exception $e) {
            $notify[] = ['error', 'Schedule is used in cron job, you can\'t delete it'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Cron schedule deleted successfully'];
        return back()->withNotify($notify);
    }

    public function log($id)
    {
        $cronJob = CronJob::findOrFail($id);
        $logs    = $cronJob->logs()->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = 'Cron Job Logs - ' . $cronJob->name;
        return view('admin.cron.logs', compact('pageTitle', 'logs', 'cronJob'));
    }
}
