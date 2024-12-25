<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SellingProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellingProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SellingProductResource;

class SellingProductController extends Controller
{
    public function __construct(protected SellingProduct $model) {}

    public function index(Request $request,$id = null){
        $data = $this->model->when($id,function($query) use ($id){
            $query->where('id',$id);
        })->when($request->query,function($query) use ($request){
            if($request->query('with') == 'user'){
                $query->with('user');
            }

            if($request->query('with') == 'sub-category'){
                $query->with('sub_category');
            }

        })->get();

        return sendResponse($data,200);
    }

    public function store(SellingProductRequest $request){

        $selling_product = $this->model->create($request->images ? $this->toArray($request->except('images')) : $this->toArray($request));

        if ($request->file('images')) {

            $imageFiles = $request->file('images');
            foreach ($imageFiles as $imageFile) {
                $imageName = storeImage($imageFile, '/product_images/'); //store image to destination folder
                $selling_product->images()->create(['name' => $imageName]);
            }

        }

        $selling_product->save();

        return sendResponse(new SellingProductResource($selling_product),200);
    }

    private function toArray($request){
        return [
            'user_id' => Auth::user()->id,
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'condition' => $request->condition,
            'price' => $request->price,
            'quantity' => $request->quantity
        ];
    }

}
