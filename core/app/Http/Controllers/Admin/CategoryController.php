<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $pageTitle  = 'All Categories';
        $categories = Category::searchable(['name'])->withCount('listings')->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.category.index', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {

        $imageValidation = $id ? 'nullable' : 'required';
        $request->validate(
            [
                'name'  => 'required|string|unique:categories,name,' . $id,
                'slug'  => 'required|string|unique:categories,slug,' . $id,
                'image' => ["$imageValidation", new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            ]
        );

        if ($id) {
            $category     = Category::findOrFail($id);
            $notification = 'Category updated successfully';
        } else {
            $category     = new Category();
            $notification = 'Category added successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), @$category->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload category image'];
                return back()->withNotify($notify);
            }
        }

        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->save();

        RequiredConfig::configured('category');
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Category::changeStatus($id);
    }

    public function feature($id)
    {
        return Category::changeStatus($id, 'is_featured');
    }
}
