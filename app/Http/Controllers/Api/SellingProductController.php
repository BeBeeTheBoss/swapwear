<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SellingProduct;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SellingProductRequest;
use App\Http\Resources\SellingProductResource;

class SellingProductController extends Controller
{
    public function __construct(protected SellingProduct $model) {}

    public function index(Request $request, $id = null)
    {
        $data = $this->model->when($id, function ($query) use ($id) {
            $query->where('id', $id);
        })->when($request->query, function ($query) use ($request) {

            $relations = explode(',', $request->query('with'));

            foreach ($relations as $relation) {

                if ($relation == 'sub-category') {
                    $query->with('sub_category');
                } else if ($relation == 'user') {
                    $query->with('user');
                }
            }
        })->get();

        return sendResponse(SellingProductResource::collection($data), 200);
    }

    public function store(SellingProductRequest $request)
    {

        $selling_product = $this->model->create($request->images ? $this->toArray($request->except('images')) : $this->toArray($request));

        if ($request->file('images')) {

            $imageFiles = $request->file('images');
            foreach ($imageFiles as $imageFile) {
                $imageName = storeImage($imageFile, '/product_images/'); //store image to destination folder
                $selling_product->images()->create(['name' => $imageName]);
            }
        }

        $selling_product->save();

        return sendResponse(new SellingProductResource($selling_product), 200);
    }

    public function update(SellingProductRequest $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $selling_product = $this->model->find($request->id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product not found');
        }

        //update other data except images
        $selling_product->update($request->images ? $this->toArray($request->except('images')) : $this->toArray($request));

        //check deleted images exist or not and if exists delete from db and storage folder
        if ($request->deleted_images) {
            $deleted_image_names = $selling_product->images()->whereIn('id', $request->deleted_images)->pluck('name'); //collect deleted image names form db
            foreach ($deleted_image_names as $din) {
                Storage::delete('public/product_images/' . $din);  //delete from storage folder
            }
            $deleted_image_names = $selling_product->images()->whereIn('id', $request->deleted_images)->delete(); //delete from db
        }

        if ($request->file('images')) {

            $imageFiles = $request->file('images');

            foreach ($imageFiles as $imageFile) {
                if (is_file($imageFile)) {      //check new images are included or not
                    $imageName = storeImage($imageFile, '/product_images/'); //store image to destination folder
                    $selling_product->images()->create(['name' => $imageName]);
                }
            }
        }

        return sendResponse(new SellingProductResource($selling_product), 200, 'Selling product updated success');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $selling_product = $this->model->find($request->id);
        if(!$selling_product){
            return sendResponse(null,404,'Selling product already deleted');
        }

        $deleted_image_names = $selling_product->images()->pluck('name');
        foreach ($deleted_image_names as $din) {
            Storage::delete('public/product_images/' . $din);
        }
        $deleted_image_names = $selling_product->images()->delete();

        $selling_product->delete();

        return sendResponse(null, 204, 'Selling product deleted success');
    }

    private function toArray($request)
    {
        return [
            'user_id' => Auth::user()->id,
            'sub_category_id' => (int) $request['sub_category_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'condition' => $request['condition'],
            'price' => (int) $request['price'],
            'quantity' => $request['quantity']
        ];
    }
}
