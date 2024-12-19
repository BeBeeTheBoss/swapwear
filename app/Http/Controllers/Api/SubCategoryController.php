<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SubCategoryService;
use App\Http\Resources\MainCategoryWithSubCategoryResource;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller
{
    public function __construct(protected SubCategoryService $service) {}

    public function index(){
        $data = $this->service->get();
        return sendResponse(SubCategoryResource::collection($data),200);
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'icon' => 'required',
            'main_category_id' => 'required'
        ]);

        $sub_category = $this->service->create($request);

        return sendResponse(new SubCategoryResource($sub_category),201,'Sub Category created successfully!');
    }

    public function update(Request $request){

        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'icon' => 'required',
            'main_category_id' => 'required'
        ]);

        $sub_category = $this->service->update($request);

        return sendResponse(new SubCategoryResource($sub_category),200,'Sub Category updated successfully!');
    }

    public function destroy(Request $request){

        $request->validate([
            'id' => 'required'
        ]);

        $this->service->delete($request);

        return sendResponse(null,200,'Sub Category deleted successfully!');
    }

}
