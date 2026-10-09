<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('display_order')->orderBy('id')->get();

        return sendResponse(BannerResource::collection($banners), 200);
    }
}
