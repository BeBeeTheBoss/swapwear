<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct(protected User $model) {}

    public function getUsers()
    {
        return $this->model->with('links')->get();
    }

    public function register(UserRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = $this->model->create($this->toArray($request->except(['image'])));

            $user = $this->model->find($user->id);
            Auth::login($user);

            $token = $user->createToken(config('app.name') . '_Token')->plainTextToken;

            if ($request->file('image')) {
                $imageName = storeImage($request->file('image'), '/profile_images/'); //store image to destination folder
                $user->image = $imageName;
                $user->save();
            }

            $data = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            DB::commit();

            return sendResponse($data, 201, "Sign up completed! Welcome to " . config('app.name'));
        } catch (\Throwable $th) {

            DB::rollBack();
            return sendResponse(null, 500, $th->getMessage());
        }
    }

    public function login(Request $request)
    {
        if (!isset($request->login_method)) {
            return sendResponse(null, 401, "Please input login method");
        }

        if ($request->login_method == 'phone') {
            $user = $this->loginWithPhoneNumber($request->phone, $request->password);
        }

        if (!$user) {
            return sendResponse(null, 401, "Login failed!, Please check your information");
        }

        Auth::loginUsingId($user->id);
        $token = $user->createToken(config('app.name') . '_Token')->plainTextToken;

        $data = [
            'user' => new UserResource($user),
            'token' => $token
        ];

        return sendResponse($data, 200, "Login success! Welcome back from " . config('app.name'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            try {

                Auth::user()->currentAccessToken()->delete();

                return sendResponse(null, 200, "Logout success! Promise me that you come back to " . config('app.name'));
            } catch (\Throwable $th) {

                return sendResponse(null, 500, $th->getMessage());
            }
        }

        return sendResponse(null, 404, "Already logged out");
    }

    private function loginWithPhoneNumber($phone, $password)
    {
        $user = $this->model->where('phone', $phone)->first();
        if (!$user) {
            return false;
        }

        if (!Hash::check($password, $user->password)) {
            return false;
        }

        return $user;
    }

    private function toArray($request)
    {
        return [
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'default_image_path' => 'default_profile.png' //set default profile image which is located on public/images folder
        ];
    }
}
