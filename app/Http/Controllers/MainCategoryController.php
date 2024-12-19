<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MainCategoryResource;
use App\Models\SubCategory;
use App\Services\MainCategoryService;

class MainCategoryController extends Controller
{
    public function __construct(protected MainCategory $model, protected MainCategoryService $service) {}

    public function index(Request $request)
    {

        $main_categories = $this->service->get($request);
        $main_categories = MainCategoryResource::collection($main_categories);

        return Inertia::render('Resources/MainCategories/Index', [
            'main_categories' => $main_categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Resources/MainCategories/Create');
    }

    public function store(Request $request)
    {

        $main_category = $this->service->create($request);

        session(['success' => 'Main Category created successfully!']);
        return redirect()->route('main-categories.get');
    }

    public function edit(Request $request)
    {

        $main_category = $this->model->find($request->id);

        return Inertia::render('Resources/MainCategories/Edit', [
            'main_category' => new MainCategoryResource($main_category)
        ]);
    }

    public function update(Request $request)
    {

        $main_category = $this->service->update($request);

        session(['success' => 'Main Category updated successfully!']);
        return redirect()->route('main-categories.get');
    }

    public function destroy(Request $request)
    {

        $this->service->delete($request);

        session(['success' => 'Main Category deleted successfully!']);
        return redirect()->route('main-categories.get');
    }
}
