<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRanking;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        $pageTitle    = "Affiliate Ranks";
        $userRankings = UserRanking::orderBy('level', 'asc')->paginate(getPaginate());
        return view('admin.ranking.list', compact('pageTitle', 'userRankings'));
    }

    public function store(Request $request, $id = 0)
    {
        $validateRule = $id ? 'nullable' : 'nullable';
        $request->validate([
            'level'           => 'required|integer|gt:0',
            'name'            => 'required',
            'direct_active'   => 'required|integer|gte:0',
            'min_plan_amount' => 'required|numeric|gte:0',
            'bonus'           => 'required|numeric|min:0',
            'icon'            => 'nullable|max:60',
            'icon_file'       => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $userRanking = UserRanking::findOrFail($id);
            $notify[]    = ['success', 'Rank updated successfully'];
        } else {
            $userRanking = new UserRanking();
            $notify[]    = ['success', 'Rank added successfully'];
        }

        if ($request->hasFile('icon_file')) {
            try {
                $userRanking->icon = fileUploader($request->icon_file, getFilePath('userRanking'), getFileSize('userRanking'), $userRanking->icon);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your icon'];
                return back()->withNotify($notify);
            }
        } elseif ($request->icon) {
            $userRanking->icon = $request->icon;
        }

        $userRanking->level           = $request->level;
        $userRanking->name            = $request->name;
        $userRanking->direct_active   = $request->direct_active;
        $userRanking->min_plan_amount = $request->min_plan_amount;
        $userRanking->bonus           = $request->bonus;
        $userRanking->save();

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return UserRanking::changeStatus($id);
    }
}
