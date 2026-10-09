<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        return Inertia::render('Banners/Index', [
            'banners' => Banner::orderBy('display_order')->orderBy('id')->get()->map(fn ($banner) => $this->payload($banner)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Banners/Create', [
            'nextOrder' => (int) Banner::max('display_order') + 1,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'display_order' => ['required', 'integer', 'min:0'],
        ]);

        $data['image'] = storeFile($request->file('image'), '/banners/');
        Banner::create($data);

        return redirect()->route('banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit(Banner $banner)
    {
        return Inertia::render('Banners/Edit', ['banner' => $this->payload($banner)]);
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'display_order' => ['required', 'integer', 'min:0'],
        ]);

        $oldImage = null;
        if ($request->hasFile('image')) {
            $oldImage = $banner->image;
            $data['image'] = storeFile($request->file('image'), '/banners/');
        } else {
            unset($data['image']);
        }

        $banner->update($data);
        if ($oldImage) Storage::disk('public')->delete('banners/'.$oldImage);

        return redirect()->route('banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        $image = $banner->image;
        $banner->delete();
        Storage::disk('public')->delete('banners/'.$image);

        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully!');
    }

    private function payload(Banner $banner): array
    {
        return [
            'id' => $banner->id,
            'image' => asset('storage/banners/'.$banner->image),
            'display_order' => $banner->display_order,
            'created_at' => $banner->created_at?->toIso8601String(),
        ];
    }
}
