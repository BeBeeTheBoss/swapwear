<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellingProductRequest;
use App\Http\Resources\SellingProductResource;
use App\Models\SellingProduct;
use App\Models\SellingProductPayment;
use function Laravel\Prompts\info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            if ($request->query('sub-category-id')) {
                $query->where('sub_category_id', $request->query('sub-category-id'));
            }

            if ($request->query('payment_id')) {
                $query->whereHas('payments', function ($query) use ($request) {
                    $query->where('payment_id', $request->query('payment_id'));
                });
            }

            if ($request->query('price_range')) {
                $start_price = (int) explode(',', $request->query('price_range'))[0];
                $end_price =  (int) explode(',', $request->query('price_range'))[1];
                $query->where('price', '>=', $start_price);
                $query->where('price', '<=', $end_price);
            }

            if ($request->query('searchKey')) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->query('searchKey')) . '%']);
            }
        })->where('is_active', true)->with('payments')->get();

        return sendResponse(SellingProductResource::collection($data), 200);
    }

    public function store(SellingProductRequest $request)
    {

        DB::beginTransaction();
        try {
            $data = $this->toArray($request);

            if ($request->file('video')) {
                $data['video'] = storeFile($request->file('video'), '/product_videos/');
            }

            $selling_product = $this->model->create($data);

            if ($request->file('images')) {

                $imageFiles = $request->file('images');
                foreach ($imageFiles as $imageFile) {
                    $imageName = storeFile($imageFile, '/product_images/'); //store image to destination folder
                    $selling_product->images()->create(['name' => $imageName]);
                }
            }

            foreach ($request->payments as $payment) {
                SellingProductPayment::create([
                    'selling_product_id' => $selling_product->id,
                    'payment_id' => $payment['id']
                ]);
            }

            $selling_product->save();

            DB::commit();

            return sendResponse(new SellingProductResource($selling_product), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, 500, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $selling_product = $this->model->find($request->id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product not found');
        }

        //update other data except images
        $data = $this->toArray($request);
        if ($request->file('video')) {
            if ($selling_product->video) {
                Storage::delete('public/product_videos/' . $selling_product->video);
            }

            $data['video'] = storeFile($request->file('video'), '/product_videos/');
        }

        $selling_product->update($data);

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
                    $imageName = storeFile($imageFile, '/product_images/'); //store image to destination folder
                    $selling_product->images()->create(['name' => $imageName]);
                }
            }
        }

        SellingProductPayment::where('selling_product_id', $selling_product->id)->delete();
        foreach ($request->payments as $payment_id) {
            SellingProductPayment::create([
                'selling_product_id' => $selling_product->id,
                'payment_id' => $payment_id
            ]);
        }

        return sendResponse(new SellingProductResource($selling_product), 200, 'Selling product updated success');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $selling_product = $this->model->find($request->id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product already deleted');
        }

        $deleted_image_names = $selling_product->images()->pluck('name');
        foreach ($deleted_image_names as $din) {
            Storage::delete('public/product_images/' . $din);
        }
        $deleted_image_names = $selling_product->images()->delete();

        if ($selling_product->video) {
            Storage::delete('public/product_videos/' . $selling_product->video);
        }

        SellingProductPayment::where('selling_product_id', $selling_product->id)->delete();

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
            'quantity' => $request['quantity'],
        ];
    }
}
