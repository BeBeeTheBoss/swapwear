<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MainCategoryResource;

class MainCategoryController extends Controller
{
    public function __construct(protected MainCategory $model)
    {

    }

    public function index(){

        $main_categories = $this->model->get();
        $main_categories = MainCategoryResource::collection($main_categories);

        return Inertia::render('Resources/MainCategories/Index',[
            'main_categories' => $main_categories
        ]);
    }

    public function create(){
        return Inertia::render('Resources/MainCategories/Create');
    }

    public function store(Request $request){
        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
        }

        $this->model->create([
            'name' => $request->name,
            'icon' => $imageName
        ]);

        session(['success' => 'Main Category created successfully!']);
        return redirect()->route('main-categories.get');
    }

    public function edit(Request $request){

        $main_category = $this->model->find($request->id);

        return Inertia::render('Resources/MainCategories/Edit',[
            'main_category' => new MainCategoryResource($main_category)
        ]);

    }

    public function update(Request $request){

        $main_category = $this->model->find($request->id);
        $main_category->name = $request->name;

        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
            $main_category->icon = $imageName;
        }

        $main_category->save();

        session(['success' => 'Main Category updated successfully!']);
        return redirect()->route('main-categories.get');

    }

    public function destroy(Request $request){
        $main_category = $this->model->find($request->id);

        Storage::delete('public/icons/' . $main_category->icon);
        $main_category->delete();

        session(['success' => 'Main Category deleted successfully!']);
        return redirect()->route('main-categories.get');

    }



}
