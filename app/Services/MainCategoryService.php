<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Storage;

class MainCategoryService
{

    public function __construct(protected MainCategory $model) {}

    public function get($request)
    {
        $main_categories = $this->model->when($request->query, function ($query) use ($request) {
            if($request->query('query') == 'sub_categories'){
                $query->with('sub_categories');
            }
        })->get();

        return $main_categories;
    }

    public function create($request)
    {

        $request->validate([
            'name' => 'required',
            'icon' => 'required',
        ]);

        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
        }

        return $this->model->create([
            'name' => $request->name,
            'icon' => $imageName
        ]);
    }

    public function update($request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'icon' => 'required',
        ]);

        $main_category = $this->model->find($request->id);
        $main_category->name = $request->name;

        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
            $main_category->icon = $imageName;
        }

        $main_category->save();

        return $main_category;
    }

    public function delete($request)
    {

        $request->validate([
            'id' => 'required',
        ]);

        $main_category = $this->model->find($request->id);

        Storage::delete('public/icons/' . $main_category->icon);

        $sub_categories = SubCategory::where('main_category_id', $main_category->id)->get();
        foreach ($sub_categories as $sub_category) {
            Storage::delete('public/icons/' . $sub_category->icon);
            $sub_category->delete();
        }

        $main_category->delete();
    }
}
