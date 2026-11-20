<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\City;
use App\Models\Country;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Cities';
        $cities    = City::with(['country'])->searchable(['name', 'country:name'])->withCount('listings')->orderBy('id', 'DESC')->paginate(getPaginate());
        $countries = Country::active()->get();
        return view('admin.city.index', compact('pageTitle', 'cities', 'countries'));
    }

    public function store(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'       => 'required|string|max:40|unique:cities,name,' . $id,
            'slug'       => 'required|string|unique:cities,slug,' . $id,
            'country_id' => 'required|integer|exists:countries,id',
            'image'      => ["$imageValidation", new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $city         = City::findOrFail($id);
            $notification = 'City updated successfully';
        } else {
            $city         = new City();
            $notification = 'New city added successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $city->image = fileUploader($request->image, getFilePath('city'), getFileSize('city'), @$city->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload city image'];
                return back()->withNotify($notify);
            }
        }

        $city->name       = $request->name;
        $city->country_id = $request->country_id;
        $city->slug       = $request->slug;
        $city->save();

        RequiredConfig::configured('city');
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return City::changeStatus($id);
    }
    public function feature($id)
    {
        return City::changeStatus($id, 'is_featured');
    }
}
