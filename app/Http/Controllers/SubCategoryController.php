<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SubCategory;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use App\Services\SubCategoryService;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\MainCategoryResource;

class SubCategoryController extends Controller
{
    public function __construct(protected SubCategory $model,protected SubCategoryService $service) {}

    public function index() {

        $data =  MainCategory::with('sub_categories')->get();

        return Inertia::render('Resources/SubCategories/Index',[
            'data' => MainCategoryResource::collection($data)
        ]);

    }

    public function create(){
        $main_categories = MainCategory::all();
        return Inertia::render('Resources/SubCategories/Create',[
            'main_categories' => $main_categories
        ]);
    }

    public function store(Request $request){

        $sub_category = $this->service->create($request);

        session(['success' => 'Sub Category created successfully!']);
        return redirect()->route('sub-categories.get');
    }

    public function edit(Request $request){

        $main_categories = MainCategory::all();
        $sub_category = $this->model->find($request->id);

        return Inertia::render('Resources/SubCategories/Edit',[
            'sub_category' => new SubCategoryResource($sub_category),
            'main_categories' => MainCategoryResource::collection($main_categories)
        ]);

    }

    public function update(Request $request){

        $sub_category = $this->service->update($request);

        session(['success' => 'Sub Category updated successfully!']);
        return redirect()->route('sub-categories.get');

    }

    public function destroy(Request $request){

        $this->service->delete($request);

        session(['success' => 'Sub Category deleted successfully!']);
        return redirect()->route('sub-categories.get');
    }


}
