<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $pageTitle   = 'All Countries';
        $countries   = Country::searchable(['name'])->orderBy('id', 'DESC')->paginate(getPaginate());
        $countryList = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.country.index', compact('pageTitle', 'countries', 'countryList'));
    }

    public function store(Request $request, $id = 0)
    {

        $request->validate([
            'name' => 'required|string|unique:countries,name,' . $id,
            'slug' => 'required|string|unique:countries,slug,' . $id,
        ]);

        if ($id) {
            $country      = Country::findOrFail($id);
            $notification = 'Country updated successfully';
        } else {
            $country      = new Country();
            $notification = 'Country added successfully';
        }

        $country->name = $request->name;
        $country->slug = $request->slug;
        $country->save();

        RequiredConfig::configured('country');
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Country::changeStatus($id);
    }
}
