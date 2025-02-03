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

        return sendResponse(new OrderResource($order), 201, 'Order created!');
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

        if ($order->status != 'order-pending' || $order->status != 'on-hold') {
            return sendResponse(null, 405, 'Seller accepted this order!, you can not refund this order now');
        }

        $order->delete();

        //back to pending status for on-hold orders
        $this->model->where('selling_product_id', $order->selling_product_id)->where('status', 'on-hold')->update(['status' => 'order-pending']);

        return sendResponse(null, 200, 'Order refunded!');
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

        $selling_product = SellingProduct::find($order->selling_product_id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product not found');
        }

        if($this->getOngoingOrdersCount($selling_product) == $selling_product->quantity) {
            return sendResponse(null, 405, 'All orders are accepted!');
        }

        $order->status = 'order-accepted';
        $order->save();

        if($this->getOngoingOrdersCount($selling_product) + 1 == $selling_product->quantity) {
            //hold other pending orders
            $this->model->where('selling_product_id', $selling_product->id)->where('status', 'order-pending')->update(['status' => 'on-hold']);

            //hold selling product
            $selling_product->status = 'on-hold';
            $selling_product->save();
        }

        return sendResponse(new OrderResource($order), 200, 'Order accepted!');
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

        $order->status = 'payment-pending';
        $order->note = $request->note ?? null;
        $order->save();

        return sendResponse(new OrderResource($order), 200, 'Order paid!');
    }

    public function acceptPayment(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->status = 'payment-accepted';
        $order->save();

        return sendResponse(new OrderResource($order), 200, 'Payment accepted!');
    }

    public function delivered(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->status = 'delivered';
        $order->save();
    }

    public function received(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->status = 'received';
        $order->save();

        $selling_product = SellingProduct::find($order->selling_product_id);
        if ($selling_product->quantity == 1) {

            //reject other holding orders
            $this->model->where('selling_product_id', $selling_product->id)->where('status', 'order-pending')->update(['status' => 'order-rejected', 'reject_note' => 'Product sold out!']);

            //set status sold out selling product
            $selling_product->status = 'sold-out';
            $selling_product->save();
        } else {

            $selling_product->product -= 1;
            $selling_product->save();
        }

        return sendResponse(new OrderResource($order), 200, 'Order received!');
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        $order->status = $order->status == 'payment-pending' ? 'payment-rejected' : 'order-rejected';
        $order->reject_note = $request->reject_note ?? null;

        if ($request->file('payment_return_screenshot')) {
            $imageName = storeImage($request->file('payment_return_screenshot'), '/payments/'); //store image to destination folder
            $order->payment_return_screenshot = $imageName;
        }

        //back to pending status for on-hold orders
        $this->model->where('selling_product_id', $order->selling_product_id)->where('status', 'on-hold')->update(['status' => 'order-pending']);

        $order->save();

        return sendResponse(new OrderResource($order), 200, 'Order rejected!');
    }

    private function getOngoingOrdersCount($selling_product)
    {
        $excludedStatuses = ['order-pending', 'on-hold', 'order-rejected', 'payment-rejected'];
        $ongoingOrdersCount = $this->model
            ->where('selling_product_id', $selling_product->id)
            ->whereNotIn('status', $excludedStatuses)
            ->count();

        return $ongoingOrdersCount;
    }

    private function toArray($request, $selling_product)
    {
        return [
            'order_code' => Str::upper(uniqid('ORD-')),
            'user_id' => Auth::user()->id,
            'selling_product_id' => $request->selling_product_id,
            'seller_id' => $selling_product->user_id,
            'quantity' => $request->quantity,
            'total_price' => $request->quantity * $selling_product->price,
            'status' => $selling_product->status
        ];
    }
}
