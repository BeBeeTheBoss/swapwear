<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct(protected User $model) {}

    public function index(Request $request, $id = null)
    {

        $data = $id ? $this->model->find($id) : $this->model
            ->when($request->name, function ($query) use ($request) {
                $name = str_replace(' ', '', $request->name);
                $query->whereRaw("REPLACE(LOWER(name), ' ', '') LIKE ?", ['%' . strtolower($name) . '%']);
            })->when($request->phone, function ($query) use ($request) {
                $query->where('phone', $request->phone);
            })
            ->inRandomOrder()
            ->paginate(20, ['*'], 'page', $request->page);

        if($id){
            if (!$data) {
                return sendResponse(null, 404, 'User not found');
            }
            new UserResource($data);
        }else{
            if (count($data) == 0) {
                return sendResponse([], 404, 'User not found');
            }
            UserResource::collection($data);
        }

        return sendResponse($data, 200);
    }

    public function update(Request $request)
    {

        $user = $this->model->find(Auth::user()->id);
        if (!$user) {
            return sendResponse(null, 404, 'User not found');
        }

        $request->name ? $user->name = $request->name : null;
        $request->phone ? $user->phone = $request->phone : null;
        $request->bio ? $user->bio = $request->bio : null;
        $request->address ? $user->address = $request->address : null;
        $user->save();
        return sendResponse(new UserResource($user), 200, 'Your data has been updated');
    }

    public function updatePfp(Request $request){
        $user = $this->model->find(Auth::user()->id);

        Storage::delete('/public/profile_images/' . $user->image);
        $user->image = null;
        $user->save();

        if($request->file('image')){
            $imageName = storeFile($request->file('image'), '/profile_images/'); //store image to destination folder
            $user->image = $imageName;
            $user->save();
        }

        return sendResponse(new UserResource($user), 200, 'Your profile picture has been updated');

    }

    public function updateNrcImages(Request $request)
    {
        $user = $this->model->find(Auth::user()->id);
        if (!$user) {
            return sendResponse(null, 404, 'User not found');
        }

        $request->validate([
            'nrc_front_image' => ['required', 'image'],
            'nrc_back_image' => ['required', 'image'],
        ]);

        if ($request->file('nrc_front_image')) {
            if ($user->nrc_front_image) {
                Storage::delete('/public/nrc_images/' . $user->nrc_front_image);
            }
            $user->nrc_front_image = storeFile($request->file('nrc_front_image'), '/nrc_images/');
        }

        if ($request->file('nrc_back_image')) {
            if ($user->nrc_back_image) {
                Storage::delete('/public/nrc_images/' . $user->nrc_back_image);
            }
            $user->nrc_back_image = storeFile($request->file('nrc_back_image'), '/nrc_images/');
        }

        $user->save();

        return sendResponse(new UserResource($user), 200, 'Your NRC images have been updated');
    }

}
