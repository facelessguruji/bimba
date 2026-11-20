<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Events';
        $events    = Event::with('user', 'category', 'city')->latest()->paginate(getPaginate());
        return view('admin.events.index', compact('pageTitle', 'events'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Events';
        $events    = Event::pending()->with('user', 'category', 'city')->latest()->paginate(getPaginate());
        return view('admin.events.index', compact('pageTitle', 'events'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Events';
        $events    = Event::approved()->with('user', 'category', 'city')->latest()->paginate(getPaginate());
        return view('admin.events.index', compact('pageTitle', 'events'));
    }

    public function create()
    {
        $pageTitle  = 'Create Company Event';
        $categories = \App\Models\EventCategory::active()->orderBy('name')->get();
        $countries  = \App\Models\Country::where('status', Status::ENABLE)->get();
        return view('admin.events.create_internal', compact('pageTitle', 'categories', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'required|string|max:5000',
            'category_id' => 'required|integer',
            'country_id'  => 'required|integer',
            'city_id'     => 'required|integer',
            'venue'       => 'required|string|max:200',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after:start_time',
            'price'       => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $event             = new Event();
        $event->title      = $request->title;
        $event->description = $request->description;
        $event->category_id = $request->category_id;
        $event->country_id  = $request->country_id;
        $event->city_id     = $request->city_id;
        $event->venue       = $request->venue;
        $event->start_time  = $request->start_time;
        $event->end_time    = $request->end_time;
        $event->event_type  = 2; // internal company event
        $event->price       = (float) $request->price;
        $event->user_id     = 0; // company-organised
        $event->status      = Status::ENABLE; // company events publish immediately

        if ($request->hasFile('cover_image')) {
            try {
                $event->cover_image = fileUploader($request->cover_image, getFilePath('event'), getFileSize('event'));
            } catch (\Throwable $e) {
                $notify[] = ['error', 'Could not upload the cover image'];
                return back()->withNotify($notify);
            }
        }

        $event->save();

        $notify[] = ['success', 'Internal company event created'];
        return back()->withNotify($notify);
    }

    public function editInternal($id)
    {
        $event      = Event::where('event_type', 2)->findOrFail($id);
        $pageTitle  = 'Edit Company Event';
        $categories = \App\Models\EventCategory::active()->orderBy('name')->get();
        $countries  = \App\Models\Country::where('status', Status::ENABLE)->get();
        return view('admin.events.edit_internal', compact('pageTitle', 'event', 'categories', 'countries'));
    }

    public function updateInternal(Request $request, $id)
    {
        $event = Event::where('event_type', 2)->findOrFail($id);
        $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'required|string|max:5000',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after:start_time',
            'price'       => 'nullable|numeric|min:0',
        ]);

        $event->title       = $request->title;
        $event->description = $request->description;
        $event->venue       = $request->venue;
        $event->start_time  = $request->start_time;
        $event->end_time    = $request->end_time;
        $event->price       = (float) $request->price;

        if ($request->hasFile('cover_image')) {
            try {
                $old = $event->cover_image;
                $event->cover_image = fileUploader($request->cover_image, getFilePath('event'), getFileSize('event'), $old);
            } catch (\Throwable $e) {
                $notify[] = ['error', 'Could not upload the cover image'];
                return back()->withNotify($notify);
            }
        }

        $event->save();
        $notify[] = ['success', 'Company event updated'];
        return back()->withNotify($notify);
    }

    public function internalStatus($id)
    {
        $event = Event::where('event_type', 2)->findOrFail($id);
        $event->status = $event->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $event->save();
        $notify[] = ['success', $event->status == Status::ENABLE ? 'Company event published' : 'Company event unpublished'];
        return back()->withNotify($notify);
    }

    public function approve($id)
    {
        $event         = Event::pending()->findOrFail($id);
        $event->status = Status::ENABLE;
        $event->save();

        // followers and interested members asked for updates on this event
        \App\Models\EventParticipation::where('event_id', $event->id)
            ->with('user')
            ->chunkById(100, function ($rows) use ($event) {
                foreach ($rows as $row) {
                    if ($row->user) {
                        notify($row->user, 'EVENT_APPROVED', [
                            'event_name' => $event->title,
                            'start_time' => showDateTime($event->start_time),
                        ]);
                    }
                }
            });

        $notify[] = ['success', 'Event approved successfully'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $event         = Event::pending()->findOrFail($id);
        $event->status = Status::LISTING_REJECTED;
        $event->rejected_reason = $request->reason;
        $event->save();
        $notify[] = ['success', 'Event rejected'];
        return back()->withNotify($notify);
    }
}
