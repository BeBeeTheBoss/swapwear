<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MainCategoryService;
use App\Http\Resources\MainCategoryResource;

class MainCategoryController extends Controller
{
    public function __construct(protected MainCategoryService $service) {}

    public function index(Request $request,$id = null){

        $main_categories = $this->service->get($request,$id);
        $main_categories = MainCategoryResource::collection($main_categories);

        return sendResponse($main_categories,200);
    }

    public function store(Request $request){

        $main_category = $this->service->create($request);

        return sendResponse(new MainCategoryResource($main_category),201,'Main Category created successfully!');
    }

    public function update(Request $request){

        $main_category = $this->service->update($request);

        return sendResponse(new MainCategoryResource($main_category),200,'Main Category updated successfully!');
    }

    public function destroy(Request $request){

        $this->service->delete($request);

        return sendResponse(null,200,'Main Category deleted successfully!');
    }

}
