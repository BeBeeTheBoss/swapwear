<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Storage;

class SubCategoryService
{
    public function __construct(protected SubCategory $model) {}


    public function get($request,$id = null)
    {
        return $this->model->when($id,function($query) use($id){
            $query->where('id', $id);
        })->when($request->query, function ($query) use ($request) {
            if($request->query('with') == 'main-category'){
                $query->with('main_category');
            }
        })->get();
    }

    public function create($request)
    {
        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
            // $request->merge(['icon' => $imageName]);
        }

        return $this->model->create([
            'name' => $request->name,
            'main_category_id' => $request->main_category_id,
            'icon' => $imageName
        ]);
    }

    public function update($request)
    {
        $sub_category = $this->model->find($request->id);
        $sub_category->name = $request->name;
        $sub_category->main_category_id = $request->main_category_id;

        if ($request->file('icon')) {
            $imageName = storeImage($request->file('icon'), '/icons/'); //store icons to destination folder
            $sub_category->icon = $imageName;
        }

        $sub_category->save();

        return $sub_category;
    }

    public function delete($request)
    {
        $sub_category = $this->model->find($request->id);

        Storage::delete('public/icons/' . $sub_category->icon);
        $sub_category->delete();
    }
}
