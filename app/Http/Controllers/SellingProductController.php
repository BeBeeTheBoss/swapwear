<?php

namespace App\Http\Controllers;

use App\Models\SellingProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SellingProductController extends Controller
{
    public function index()
    {
        return Inertia::render('SellingProducts/Index', [
            'products' => SellingProduct::with(['user:id,name,image', 'sub_category:id,name', 'images:id,selling_product_id,name'])
                ->withCount('orders')->latest()->get()->map(fn ($product) => $this->payload($product)),
        ]);
    }

    public function show(SellingProduct $sellingProduct)
    {
        $sellingProduct->load(['user:id,name,phone,email,image,is_approved', 'sub_category.main_category', 'images', 'payments.payment']);
        $sellingProduct->loadCount('orders');
        return Inertia::render('SellingProducts/Show', ['product' => $this->payload($sellingProduct, true)]);
    }

    public function destroy(SellingProduct $sellingProduct)
    {
        $images = $sellingProduct->images()->pluck('name');
        $video = $sellingProduct->video;
        DB::transaction(function () use ($sellingProduct) {
            $sellingProduct->images()->delete();
            $sellingProduct->payments()->delete();
            $sellingProduct->delete();
        });
        foreach ($images as $image) Storage::disk('public')->delete('product_images/'.$image);
        if ($video) Storage::disk('public')->delete('product_videos/'.$video);
        return redirect()->route('selling-products.index')->with('success', 'Product deleted successfully!');
    }

    private function payload(SellingProduct $product, bool $details = false): array
    {
        $data = [
            'id'=>$product->id, 'name'=>$product->name, 'description'=>$product->description,
            'condition'=>$product->condition, 'quantity'=>$product->quantity, 'price'=>(int)$product->price,
            'status'=>$product->status, 'is_active'=>(bool)$product->is_active, 'orders_count'=>$product->orders_count ?? 0,
            'created_at'=>$product->created_at?->toIso8601String(),
            'seller'=>$product->user ? ['id'=>$product->user->id,'name'=>$product->user->name,'image'=>$product->user->image ? asset('storage/profile_images/'.$product->user->image):null] : null,
            'category'=>$product->sub_category ? ['id'=>$product->sub_category->id,'name'=>$product->sub_category->name] : null,
            'cover'=>$product->images->first() ? asset('storage/product_images/'.$product->images->first()->name) : null,
        ];
        if ($details) $data += [
            'images'=>$product->images->map(fn($image)=>['id'=>$image->id,'url'=>asset('storage/product_images/'.$image->name)]),
            'video'=>$product->video ? asset('storage/product_videos/'.$product->video):null,
            'seller_details'=>$product->user?->only(['id','name','phone','email','is_approved']),
            'main_category'=>$product->sub_category?->main_category?->name,
            'payment_methods'=>$product->payments->map(fn($item)=>$item->payment?->only(['id','name']))->filter()->values(),
        ];
        return $data;
    }
}
