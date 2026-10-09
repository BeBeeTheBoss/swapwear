<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Orders/Index', [
            'orders' => Order::with(['user:id,name,image', 'seller:id,name', 'selling_product:id,name'])
                ->latest()->get()->map(fn ($order) => $this->payload($order)),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user:id,name,email,phone,image,address', 'seller:id,name,email,phone,image,address', 'selling_product:id,name,condition,price', 'payment:id,name']);
        return Inertia::render('Orders/Show', ['order' => $this->payload($order, true)]);
    }

    private function payload(Order $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id, 'code' => $order->order_code, 'quantity' => $order->quantity,
            'total_price' => (int) $order->total_price, 'status' => strtolower($order->status),
            'created_at' => $order->created_at?->toIso8601String(), 'updated_at' => $order->updated_at?->toIso8601String(),
            'buyer' => $order->user ? ['id' => $order->user->id, 'name' => $order->user->name, 'image' => $this->profileImage($order->user->image)] : null,
            'seller' => $order->seller ? ['id' => $order->seller->id, 'name' => $order->seller->name] : null,
            'product' => $order->selling_product ? ['id' => $order->selling_product->id, 'name' => $order->selling_product->name] : null,
        ];
        if ($detailed) {
            $data += [
                'buyer_details' => $order->user?->only(['id','name','email','phone','address']),
                'seller_details' => $order->seller?->only(['id','name','email','phone','address']),
                'product_details' => $order->selling_product?->only(['id','name','condition','price']),
                'payment' => $order->payment?->only(['id','name']), 'note' => $order->note, 'reject_note' => $order->reject_note,
                'payment_screenshot' => $order->payment_screenshot ? asset('storage/payments/'.$order->payment_screenshot) : null,
                'payment_return_screenshot' => $order->payment_return_screenshot ? asset('storage/payments/'.$order->payment_return_screenshot) : null,
            ];
        }
        return $data;
    }

    private function profileImage(?string $image): ?string
    {
        return $image ? asset('storage/profile_images/'.$image) : null;
    }
}
