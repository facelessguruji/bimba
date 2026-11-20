<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Plans';
        $plans     = Plan::searchable(['title'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function store(Request $request, $id = 0)
    {

        $request->validate([
            'title'         => 'required',
            'yearly_price'  => 'required|gte:0',
            'listings'      => 'required|integer|gte:0',
            'amenities'     => 'required|integer|gte:0',
            'images'        => 'required|integer|gte:0',
            'videos'        => 'required|integer|gte:0',
            'events'        => 'required|integer|gte:0',
        ]);

        if ($id) {
            $category     = Plan::findOrFail($id);
            $notification = 'Plan updated successfully';
        } else {
            $category     = new Plan();
            $notification = 'Plan added successfully';
        }

        $category->title         = $request->title;
        $category->monthly_price = $request->yearly_price; // column kept for schema compatibility; plans are yearly-only
        $category->yearly_price  = $request->yearly_price;
        $category->listings      = $request->listings;
        $category->amenities     = $request->amenities;
        $category->images        = $request->images;
        $category->videos        = $request->videos;
        $category->events        = $request->events;
        $category->map           = $request->map ? Status::YES : Status::NO;
        $category->save();

        RequiredConfig::configured('plan');
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }
}
