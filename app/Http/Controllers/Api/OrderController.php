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

    public function index(Request $request)
    {

        $orders = $this->model
            ->when($request->pov, function ($query) use ($request) {

                if ($request->pov == 'seller') {
                    $query->where('seller_id', Auth::user()->id);
                } else if ($request->pov == 'user') {
                    $query->where('user_id', Auth::user()->id);
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

        return sendResponse(OrderResource::collection($orders), 200);
    }

    public function store(OrderRequest $request)
    {
        $selling_product = SellingProduct::find($request->selling_product_id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product not found');
        }

        $order = $this->model->create($this->toArray($request, $selling_product));

        return sendResponse(new OrderResource($order), 201, 'Order created successfully!');
    }

    public function refund(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'note' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        if ($order->status != 'pending') {
            return sendResponse(null, 401, 'Seller accepted this order!, you can not refund this order');
        }

        $order->delete();

        return sendResponse(null, 200, 'Order refunded successfully!');
    }

    public function accept(Request $request)
    {

        $request->validate([
            'id' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->status = 'accepted';
        $order->save();

        return sendResponse(new OrderResource($order), 200, 'Order accepted successfully!');
    }

    public function makePayment(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'payment_id' => 'required',
            'payment_screenshot' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->payment_id = $request->payment_id;

        if ($request->file('payment_screenshot')) {
            $imageName = storeImage($request->file('payment_screenshot'), '/payments/'); //store image to destination folder
            $order->payment_screenshot = $imageName;
        }

        $order->status = 'paid';
        $order->note = $request->note ?? null;
        $order->save();

        return sendResponse(new OrderResource($order), 200, 'Order paid successfully!');
    }

    public function reject(Request $request){
        $request->validate([
            'id' => 'required',
        ]);
    }

    private function toArray($request, $selling_product)
    {
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
