<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Country;
use App\Models\Event;
use App\Models\EventCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $pageTitle = 'My Events';
        $events    = Event::where('user_id', auth()->id())->with('category', 'city', 'country')->latest()->paginate(getPaginate());
        return view('Template::user.events.index', compact('pageTitle', 'events'));
    }

    public function create()
    {
        $pageTitle  = 'Create Event';
        $categories = EventCategory::active()->orderBy('name')->get();
        $countries  = Country::where('status', Status::ENABLE)->get();
        return view('Template::user.events.form', compact('pageTitle', 'categories', 'countries'));
    }

    public function edit($id)
    {
        $event      = Event::where('user_id', auth()->id())->findOrFail($id);
        $pageTitle  = 'Edit Event';
        $categories = EventCategory::active()->orderBy('name')->get();
        $countries  = Country::where('status', Status::ENABLE)->get();
        return view('Template::user.events.form', compact('pageTitle', 'event', 'categories', 'countries'));
    }

    public function store(Request $request)
    {
        return $this->saveEvent($request, 0);
    }

    public function update(Request $request, $id)
    {
        return $this->saveEvent($request, $id);
    }

    protected function saveEvent(Request $request, $id)
    {
        // events are a plan entitlement (D-law): the active plan's events
        // quota caps how many events a member can host per year. Creating
        // counts pending + approved events against the quota.
        if (!$id) {
            $plan  = auth()->user()->activePlan;
            $quota = (int) ($plan->events ?? 0);
            $used  = Event::where('user_id', auth()->id())->whereIn('status', [0, 1])->count();
            if ($used >= $quota) {
                $notify[] = ['error', 'Your plan allows ' . $quota . ' event' . ($quota == 1 ? '' : 's') . ' per year. Upgrade your plan to host more events.'];
                return back()->withNotify($notify);
            }
        }

        $imageRule = $id ? 'nullable' : 'required';

        $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'required|string|max:5000',
            'category_id' => 'required|integer|exists:event_categories,id',
            'country_id'  => 'required|integer|exists:countries,id',
            'city_id'     => 'required|integer|exists:cities,id',
            'venue'       => 'required|string|max:200',
            'start_time'  => 'required|date|after:now',
            'end_time'    => 'required|date|after:start_time',
            'cover_image' => [$imageRule, 'image', 'max:500', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $event = Event::where('user_id', auth()->id())->findOrFail($id);
        } else {
            $event       = new Event();
            $event->user_id = auth()->id();
        }

        $event->title       = $request->title;
        $event->description = $request->description;
        $event->category_id = $request->category_id;
        $event->country_id  = $request->country_id;
        $event->city_id     = $request->city_id;
        $event->venue       = $request->venue;
        $event->start_time  = $request->start_time;
        $event->end_time    = $request->end_time;
        $event->status      = Status::LISTING_PENDING;

        if ($request->hasFile('cover_image')) {
            try {
                $old = $event->cover_image;
                $event->cover_image = fileUploader($request->cover_image, getFilePath('event'), getFileSize('event'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload the cover image'];
                return back()->withNotify($notify);
            }
        }

        $event->save();

        if (!$id) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = auth()->id();
            $adminNotification->title     = 'New event submitted for approval';
            $adminNotification->click_url = route('admin.events.index');
            $adminNotification->save();
        }

        $notify[] = ['success', $id ? 'Event updated successfully' : 'Event submitted for approval'];
        return to_route('user.events.index')->withNotify($notify);
    }
}
