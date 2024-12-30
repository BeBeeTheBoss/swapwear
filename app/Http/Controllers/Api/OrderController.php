<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SellingProduct;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Crypt;

class OrderController extends Controller
{
    public function __construct(protected Order $model) {}

    public function index(Request $request){

        $orders = $this->model
        ->when($request->pov, function ($query) use ($request) {

            if($request->pov == 'seller'){
                $query->where('seller_id',Auth::user()->id);
            }else if($request->pov == 'user'){
                $query->where('user_id',Auth::user()->id);
            }

        })
        ->when($request->query, function ($query) use ($request) {

            $relations = explode(',', $request->query('with'));

            foreach ($relations as $relation) {

                if ($relation == 'user') {
                    $query->with('user');
                } else if ($relation == 'seller') {
                    $query->with('seller');
                }
            }
        })->get();

        return sendResponse(OrderResource::collection($orders),200);
    }

    public function store(OrderRequest $request){
        $selling_product = SellingProduct::find($request->selling_product_id);
        if(!$selling_product){
            return sendResponse(null,404,'Selling product not found');
        }

        $order = $this->model->create($this->toArray($request,$selling_product));

        return sendResponse(new OrderResource($order),201,'Order created successfully!');

    }

    private function toArray($request,$selling_product){
        return [
            'order_code' => Str::upper(uniqid('ORD-')),
            'user_id' => Auth::user()->id,
            'selling_product_id' => $request->selling_product_id,
            'seller_id' => $selling_product->user_id,
            'quantity' => $request->quantity,
            'total_price' => $request->quantity * $selling_product->price
        ];
    }

}
