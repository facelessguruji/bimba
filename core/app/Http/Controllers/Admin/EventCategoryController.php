<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        $pageTitle  = 'Event Categories';
        $categories = EventCategory::orderBy('name')->paginate(getPaginate());
        return view('admin.events.categories', compact('pageTitle', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:event_categories,name']);
        $category       = new EventCategory();
        $category->name = $request->name;
        $category->slug = strtolower(str_replace(' ', '-', $request->name));
        $category->save();
        $notify[] = ['success', 'Category added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:100|unique:event_categories,name,' . $id]);
        $category       = EventCategory::findOrFail($id);
        $category->name = $request->name;
        $category->slug = strtolower(str_replace(' ', '-', $request->name));
        $category->save();
        $notify[] = ['success', 'Category updated successfully'];
        return back()->withNotify($notify);
    }
}
