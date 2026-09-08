<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Notification;
use App\Models\Order;
use App\Models\SellingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                    } else if ($relation == 'selling-product') {
                        $query->with('selling_product', function ($query) {
                            $query->with('payments');
                        });
                    }
                }
            })->orderBy('created_at', 'desc')->get();

        return sendResponse(OrderResource::collection($orders), 200);
    }

    public function store(OrderRequest $request)
    {
        $selling_product = SellingProduct::find($request->selling_product_id);
        if (!$selling_product) {
            return sendResponse(null, 404, 'Selling product not found');
        }

        if ($selling_product->quantity < $request->quantity) {
            return sendResponse(null, 404, 'Please order in available quantity');
        }

        if ($selling_product->status == 'sold-out' || $selling_product->status == 'on-hold') {
            return sendResponse(null, 405, 'You cannot order this product now!');
        }

        $order = $this->model->create($this->toArray($request, $selling_product));

        $this->notifyUser(
            $order->seller_id,
            'New Order',
            "You have received a new order ({$order->order_code}) for {$selling_product->name}."
        );

        return sendResponse(new OrderResource($order), 201, 'Order created!');
    }

    public function refund(Request $request)
    {

        $request->validate([
            'id' => 'required'
        ]);

        info($request->all());

        $order = $this->model->find($request->id);
        if (!$order) {
            return sendResponse(null, 404, 'Order not found');
        }

        if (!in_array($order->status, ['order-pending', 'on-hold', 'order-accepted'])) {
            return sendResponse(null, 405, 'You just made a payment, you cannot refund this order now.');
        }

        if ($order->status == "order-accepted") {
            $selling_product = SellingProduct::find($order->selling_product_id);
            if (!$selling_product) {
                return sendResponse(null, 404, 'Selling product not found');
            }

            $selling_product->quantity += $order->quantity;
            $selling_product->status = 'selling';
            $selling_product->save();

            //back to pending status for on-hold orders
            $heldOrders = $this->model
                ->where('selling_product_id', $order->selling_product_id)
                ->where('status', 'on-hold')
                ->get();

            $heldOrders->each(function ($heldOrder) {
                $heldOrder->update(['status' => 'order-pending']);
                $this->notifyUser(
                    $heldOrder->user_id,
                    'Order Available Again',
                    "Your order ({$heldOrder->order_code}) is pending again."
                );
            });
        }

        $this->notifyUser(
            $order->seller_id,
            'Order Cancelled',
            "The buyer cancelled order {$order->order_code}."
        );

        $order->delete();

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

        // if ($this->getOngoingOrdersCount($selling_product) == $selling_product->quantity) {
        //     return sendResponse(null, 405, 'All orders are accepted!');
        // }

        $order->status = 'order-accepted';
        $order->save();

        $this->notifyUser(
            $order->user_id,
            'Order Accepted',
            "Your order ({$order->order_code}) has been accepted."
        );

        $selling_product->quantity -= $order->quantity;
        $selling_product->save();

        if ($selling_product->quantity == 0) {
            $selling_product->status = 'on-hold';
            $selling_product->save();
            $pendingOrders = $this->model
                ->where('selling_product_id', $selling_product->id)
                ->where('status', 'order-pending')
                ->get();

            $pendingOrders->each(function ($pendingOrder) {
                $pendingOrder->update(['status' => 'on-hold']);
                $this->notifyUser(
                    $pendingOrder->user_id,
                    'Order On Hold',
                    "Your order ({$pendingOrder->order_code}) is currently on hold."
                );
            });
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
            $imageName = storeFile($request->file('payment_screenshot'), '/payments/'); //store image to destination folder
            $order->payment_screenshot = $imageName;
        }

        $order->status = 'payment-pending';
        $order->note = $request->note ?? null;
        $order->save();

        $selling_product = SellingProduct::find($order->selling_product_id);

        $this->notifyUser(
            $order->seller_id,
            'Payment Submitted',
            "Payment for order {$order->order_code} has been submitted for review."
        );

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

        $this->notifyUser(
            $order->user_id,
            'Payment Accepted',
            "Your payment for order {$order->order_code} has been accepted."
        );

        $selling_product = SellingProduct::find($order->selling_product_id);

        if ($selling_product->status == 'on-hold' && $this->model->where('selling_product_id', $selling_product->id)->where('status', 'payment-pending')->count() == 0) {
            $selling_product->status = 'sold-out';
            $heldOrders = $this->model
                ->where('selling_product_id', $selling_product->id)
                ->where('status', 'on-hold')
                ->get();

            $heldOrders->each(function ($heldOrder) {
                $heldOrder->update([
                    'status' => 'order-rejected',
                    'reject_note' => 'Product sold out',
                ]);
                $this->notifyUser(
                    $heldOrder->user_id,
                    'Order Rejected',
                    "Your order ({$heldOrder->order_code}) was rejected because the product is sold out."
                );
            });
            $selling_product->save();
        }

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

        $this->notifyUser(
            $order->user_id,
            'Order Delivered',
            "Your order ({$order->order_code}) has been marked as delivered."
        );

        return sendResponse(new OrderResource($order), 200, 'Order delivered!');
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

        $this->notifyUser(
            $order->seller_id,
            'Order Received',
            "The buyer has confirmed receipt of order {$order->order_code}."
        );

        // $selling_product = SellingProduct::find($order->selling_product_id);
        // if ($selling_product->quantity == $order->quantity) {

        //     //reject other holding orders
        //     $this->model->where('selling_product_id', $selling_product->id)->where('status', 'order-pending')->update(['status' => 'order-rejected', 'reject_note' => 'Product sold out!']);

        //     //set status sold out selling product
        //     $selling_product->status = 'sold-out';
        //     $selling_product->save();
        // } else {

        //     $selling_product->product -= $order->quantity;
        //     $selling_product->save();
        // }

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

        $isPaymentRejection = $order->status == 'payment-pending';
        $order->status = $isPaymentRejection ? 'payment-rejected' : 'order-rejected';
        $order->reject_note = $request->reject_note ?? null;

        if ($request->file('payment_return_screenshot')) {
            $imageName = storeFile($request->file('payment_return_screenshot'), '/payments/'); //store image to destination folder
            $order->payment_return_screenshot = $imageName;
        }

        //back to pending status for on-hold orders
        $heldOrders = $this->model
            ->where('selling_product_id', $order->selling_product_id)
            ->where('status', 'on-hold')
            ->get();

        $heldOrders->each(function ($heldOrder) {
            $heldOrder->update(['status' => 'order-pending']);
            $this->notifyUser(
                $heldOrder->user_id,
                'Order Available Again',
                "Your order ({$heldOrder->order_code}) is pending again."
            );
        });

        $selling_product = SellingProduct::find($order->selling_product_id);

        $selling_product->quantity += $order->quantity;

        if ($selling_product->status == 'sold-out' || $selling_product->status == 'on-hold') {
            $selling_product->status = 'selling';
        }

        $selling_product->save();
        $order->save();

        $title = $isPaymentRejection ? 'Payment Rejected' : 'Order Rejected';
        $message = $isPaymentRejection
            ? "Your payment for order {$order->order_code} was rejected."
            : "Your order ({$order->order_code}) was rejected.";

        if ($order->reject_note) {
            $message .= ' Reason: ' . $order->reject_note;
        }

        $this->notifyUser($order->user_id, $title, $message);

        return sendResponse(new OrderResource($order), 200, 'Order rejected!');
    }

    private function getOngoingOrdersCount($selling_product)
    {
        $excludedStatuses = ['order-pending', 'on-hold', 'order-rejected', 'payment-rejected'];
        $ongoingOrdersCount = $this->model
            ->where('selling_product_id', $selling_product->id)
            ->whereNotIn('status', $excludedStatuses)
            ->sum('quantity');

        return $ongoingOrdersCount;
    }

    private function notifyUser(int $userId, string $title, string $message): void
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
        ]);

        $notification->users()->attach($userId);

        try {
            send_notification_FCM($title, $message, $userId);
        } catch (\Throwable $exception) {
            Log::warning('Failed to send an order push notification.', [
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'error' => $exception->getMessage(),
            ]);
        }
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
            'status' => 'order-pending'
        ];
    }
}
