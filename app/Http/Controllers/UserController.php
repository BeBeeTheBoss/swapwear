<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::where('role', '!=', 'admin')->withCount(['selling_products'])->latest()->get()->map(fn ($user) => $this->payload($user)),
        ]);
    }

    public function show(User $user)
    {
        abort_if($user->role === 'admin', 403);
        $user->loadCount(['selling_products']);
        $user->load(['links']);
        $ordersCount = \App\Models\Order::where('user_id', $user->id)->count();
        return Inertia::render('Users/Show', ['user' => $this->payload($user) + ['orders_count' => $ordersCount, 'links' => $user->links]]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->role === 'admin' || $request->user()?->is($user), 403, 'Admin accounts cannot be deleted here.');
        foreach (['image' => 'profile_images/', 'nrc_front_image' => 'nrc_images/', 'nrc_back_image' => 'nrc_images/'] as $field => $path) {
            if ($user->{$field}) Storage::disk('public')->delete($path.$user->{$field});
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    private function payload(User $user): array
    {
        return [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'address' => $user->address, 'image' => $user->image ? asset('storage/profile_images/'.$user->image) : null,
            'role' => $user->role, 'trust_level' => $user->trust_level, 'sell_count' => $user->sell_count,
            'reward_points' => $user->reward_points, 'is_approved' => (bool) $user->is_approved,
            'selling_products_count' => $user->selling_products_count ?? 0, 'created_at' => $user->created_at?->toIso8601String(),
            'nrc_front_image' => $user->nrc_front_image ? asset('storage/nrc_images/'.$user->nrc_front_image) : null,
            'nrc_back_image' => $user->nrc_back_image ? asset('storage/nrc_images/'.$user->nrc_back_image) : null,
        ];
    }
}
