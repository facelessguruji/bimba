<?php

namespace App\Http\Controllers\User;

use App\Models\City;
use App\Models\Country;
use App\Models\Listing;
use App\Models\Category;
use App\Constants\Status;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ListingController extends Controller
{
    public function myListings()
    {
        $pageTitle = 'My Listings';
        $cities    = City::active()->get();
        $listings  = Listing::with('category', 'city')->where('user_id', auth()->user()->id)->searchable(['title'])->filter(['city_id', 'status'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.listing.all', compact('pageTitle', 'cities', 'listings'));
    }

    public function form()
    {
        $pageTitle  = 'Create Listing';
        $cities     = City::active()->get();
        $categories = Category::active()->get();
        $activePlan = auth()->user()->activePlan;
        $countries  = Country::with('cities')->whereHas('cities', function ($q) {
            $q->where('status', Status::ENABLE);
        })
            ->active()
            ->get();
        return view('Template::user.listing.form', compact('pageTitle', 'cities', 'categories', 'activePlan', 'countries'));
    }

    public function edit($id)
    {
        $pageTitle  = 'Edit listing';
        $cities     = City::active()->get();
        $categories = Category::active()->get();
        $listing    = Listing::where('user_id', auth()->user()->id)->with('images')->findOrFail($id);

        $images = [];
        foreach ($listing->images as $key => $image) {
            $img['id']  = $image->id;
            $img['src'] = getImage(getFilePath('listing') . '/' . $image->name);
            $images[]   = $img;
        }
        $activePlan = auth()->user()->activePlan;
        $countries  = Country::with('cities')->active()->get();
        return view('Template::user.listing.form', compact('pageTitle', 'cities', 'categories', 'listing', 'images', 'activePlan', 'countries'));
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'bullets' => 'required|string|max:2000',
        ]);

        if (!\App\Lib\AiService::enabled('listing_writer')) {
            $notify[] = ['error', 'AI listing writer is not available'];
            return back()->withNotify($notify);
        }

        $content = \App\Lib\AiService::chat([
            ['role' => 'system', 'content' => 'You write business directory listings. Reply ONLY with JSON: {"title": "...", "description": "2-3 persuasive sentences", "tags": ["tag1","tag2","tag3"]}.'],
            ['role' => 'user', 'content' => 'Business bullets:\n' . $request->bullets],
        ], 'listing_writer', auth()->id(), 500);

        $data = \App\Lib\AiService::extractJson($content);

        if (!$data || empty($data['title'])) {
            $notify[] = ['error', 'AI could not generate content, please try again'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'AI generated the listing copy. Review and edit it before saving.'];
        return back()
            ->withInput(['title' => $data['title'] ?? '', 'description' => $data['description'] ?? ''])
            ->with('aiTags', $data['tags'] ?? [])
            ->withNotify($notify);
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request, $id);

        if ($id) {
            // ownership: a member can only ever update their own listing
            $listing                = Listing::where('user_id', auth()->user()->id)->findOrFail($id);
            $notification           = 'Listing updated successfully';
            $adminNotificationTitle = 'Listing approval request';
        } else {
            $listing                = new Listing();
            $notification           = 'Listing created successfully';
            $adminNotificationTitle = 'New listing approval request';
        }

        if ($request->hasFile('display_image')) {
            try {
                $old                    = $listing->display_image;
                $listing->display_image = fileUploader($request->display_image, getFilePath('listing'), getFileSize('listing'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your listing image'];
                return back()->withNotify($notify);
            }
        }

        $user = auth()->user();

        $listing->user_id     = $user->id;
        if (!$id) {
            // the public reference is minted once at creation - an edit must
            // never silently change it (it is shown/linked on the public page)
            $listing->listing_id = getTrx(8);
        }
        $listing->title       = $request->title;
        // member-supplied HTML is purified before storage - the description renders raw on the public listing page
        $purifier            = new \HTMLPurifier();
        $listing->description = $purifier->purify($request->description);
        $listing->category_id = $request->category_id;
        $listing->city_id     = $request->city_id;
        $listing->country_id  = $request->country_id;
        $listing->location    = $request->location;
        $listing->open_time   = $request->open_time;
        $listing->close_time  = $request->close_time;
        $listing->day_off     = $request->day_off ?? [];
        $listing->amenities   = $request->amenities ?? [];
        $listing->tags        = $request->tags ?? [];
        $listing->videos      = $request->videos ?? [];
        $listing->status      = Status::LISTING_PENDING;
        $listing->map         = $request->map;
        $listing->save();
        
        $imageUpload = $this->insertImages($request, $listing, $id);
        if ($imageUpload) {
            // image upload failed - surface the error instead of a misleading success
            return $imageUpload;
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = $adminNotificationTitle;
        $adminNotification->click_url = route('admin.listing.details', $listing->id);
        $adminNotification->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    protected function validation($request, $id)
    {

        $imageRules = $id ? 'nullable' : 'required';
        $user       = auth()->user();
        $plan       = $user->activePlan;

        if (!$plan) {
            throw ValidationException::withMessages(['error' => 'Your current plan has expired!']);
        }

        $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:categories,id',
            'country_id'        => 'required|exists:countries,id',
            'city_id'           => 'required|exists:cities,id',
            'location'          => 'required|string|max:255',
            'tags'              => 'nullable|array|min:1|max:10',
            'tags.*'            => 'required|string',
            'amenities'         => ['nullable', 'array', 'max:' . $plan->amenities],
            'amenities.*.name'  => 'required|string',
            'amenities.*.value' => 'required|string',
            'day_off'           => 'nullable|array|min:1|max:7',
            'day_off.*'         => 'string|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'open_time'         => 'nullable|date_format:H:i',
            'close_time'        => 'nullable|required_with:open_time|date_format:H:i',
            'display_image'     => [$imageRules, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'images'            => ['nullable', 'array', 'max:' . $plan->images],
            'images.*'          => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'videos'            => ['nullable', 'array', 'max:' . $plan->videos],
            'videos.*'          => 'required|url',
            'map'               => 'nullable|url',
        ], [
            'amenities.max'     => "Please follow maximum limit of amenities",
            'amenities.*.name'  => "Amenity name fields must not be empty",
            'amenities.*.value' => "Amenity value fields must not be empty",
        ]);

        if (!$id) {

            $listingsCount = Listing::approved()->where('user_id', $user->id)->count();

            // the plan's listing slots are enforced with a throw (a returned
            // redirect from this helper would be discarded by the caller) and
            // a listings value of 0 means unlimited
            if ($plan->listings > 0 && $listingsCount >= $plan->listings) {
                throw ValidationException::withMessages(['error' => 'The maximum amount of listings for your plan has been reached. Please upgrade your plan.']);
            }
        }


        $oldImages     = $request->old ?? [];
        $newImages     = $request->images ?? [];
        $galleryImages = $oldImages + $newImages;


        if ($plan->images < count($galleryImages)) {
            throw ValidationException::withMessages(['error' => 'The maximum amount of image has already been uploaded']);
        }


        if (!$plan->allow_map && $request->map) {
            throw ValidationException::withMessages(['error' => 'You are not allowed to add map']);
        }
    }

    protected function insertImages($request, $listing, $id)
    {
        $path = getFilePath('listing');

        $hasImages = $request->file('images');
        if ($hasImages) {
            $size   = getFileSize('listing');
            $images = [];

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            foreach ($hasImages as $file) {
                try {
                    $name              = fileUploader($file, $path, $size);
                    $image             = new ListingImage();
                    $image->listing_id = $listing->id;
                    $image->name       = $name;
                    $images[]          = $image;
                } catch (\Exception $exp) {
                    // mid-batch failure - unlink the files of this batch that
                    // were already moved so no orphaned uploads remain
                    foreach ($images as $uploadedImage) {
                        @unlink($path . '/' . $uploadedImage->name);
                    }
                    $notify[] = ['error', 'Couldn\'t upload listing images'];
                    return back()->withNotify($notify);
                }
            }
        }

        if ($id) {
            // old-gallery deletion runs only after the whole new batch
            // uploaded successfully - a failed upload must never delete
            // the existing images (runs before saveMany so the lazy-loaded
            // gallery still holds only the old rows)
            $this->removeImages($request, $listing, $path);
        }

        if ($hasImages) {
            $listing->images()->saveMany($images);
        }
    }

    protected function removeImages($request, $listing, $path)
    {
        $previousImages = $listing->images->pluck('id')->toArray();
        $imageToRemove  = array_values(array_diff($previousImages, $request->old ?? []));
        foreach ($imageToRemove as $item) {
            $listingImage = ListingImage::find($item);
            @unlink($path . '/' . $listingImage->name);
            $listingImage->delete();
        }
    }

    public function switchStatus($id, $status)
    {
        $user    = auth()->user();
        $listing = Listing::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        if ($status == Status::LISTING_DISABLED) {
            $notification = "Listing disabled successfully";
        } elseif ($status == Status::LISTING_APPROVED) {
            $status       = Status::LISTING_PENDING;
            $notification = "Your approval request has been sent. Please wait for admin approval.";

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'Listing approval request';
            $adminNotification->click_url = route('admin.listing.details', $listing->id);
            $adminNotification->save();
        } else {
            abort(403);
        }

        $listing->status = $status;
        $listing->save();
        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }
}
