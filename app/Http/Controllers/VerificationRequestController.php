<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\VerifiedApproveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VerificationRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('VerificationRequests/Index', [
            'requests' => VerifiedApproveRequest::with(['user:id,name,phone,email,image,is_approved', 'reviewer:id,name'])
                ->latest()->get()->map(fn ($item) => $this->payload($item)),
        ]);
    }

    public function show(VerifiedApproveRequest $verificationRequest)
    {
        $verificationRequest->load(['user:id,name,phone,email,address,image,is_approved,created_at', 'reviewer:id,name']);
        return Inertia::render('VerificationRequests/Show', ['verificationRequest' => $this->payload($verificationRequest, true)]);
    }

    public function approve(Request $request, VerifiedApproveRequest $verificationRequest)
    {
        DB::transaction(function () use ($request, $verificationRequest) {
            $item = VerifiedApproveRequest::lockForUpdate()->findOrFail($verificationRequest->id);
            abort_unless($item->status === 'pending', 422, 'Only pending requests can be approved.');
            $user = User::lockForUpdate()->findOrFail($item->user_id);
            $user->update(['is_approved' => true, 'nrc_front_image' => $item->nrc_front_image, 'nrc_back_image' => $item->nrc_back_image]);
            $item->update(['status' => 'approved', 'reviewed_by' => $request->user()->id, 'review_note' => $request->input('review_note'), 'reviewed_at' => now()]);
            $notification = Notification::create(['title' => 'Identity Verified', 'message' => 'Your identity verification has been approved.']);
            $notification->users()->attach($user->id);
        });
        return redirect()->route('verification-requests.show', $verificationRequest)->with('success', 'User verification approved successfully!');
    }

    public function reject(Request $request, VerifiedApproveRequest $verificationRequest)
    {
        $data = $request->validate(['review_note' => ['required', 'string', 'max:1000']]);
        DB::transaction(function () use ($request, $verificationRequest, $data) {
            $item = VerifiedApproveRequest::lockForUpdate()->findOrFail($verificationRequest->id);
            abort_unless($item->status === 'pending', 422, 'Only pending requests can be rejected.');
            $item->user()->update(['is_approved' => false]);
            $item->update(['status' => 'rejected', 'reviewed_by' => $request->user()->id, 'review_note' => $data['review_note'], 'reviewed_at' => now()]);
            $notification = Notification::create(['title' => 'Verification Needs Attention', 'message' => 'Your identity verification was not approved. Reason: '.$data['review_note']]);
            $notification->users()->attach($item->user_id);
        });
        return redirect()->route('verification-requests.show', $verificationRequest)->with('success', 'Verification request rejected.');
    }

    private function payload(VerifiedApproveRequest $item, bool $details = false): array
    {
        $data = [
            'id' => $item->id, 'status' => $item->status, 'created_at' => $item->created_at?->toIso8601String(),
            'reviewed_at' => $item->reviewed_at?->toIso8601String(), 'review_note' => $item->review_note,
            'front_image' => asset('storage/nrc_images/'.$item->nrc_front_image), 'back_image' => asset('storage/nrc_images/'.$item->nrc_back_image),
            'user' => $item->user ? ['id'=>$item->user->id,'name'=>$item->user->name,'phone'=>$item->user->phone,'email'=>$item->user->email,'image'=>$item->user->image ? asset('storage/profile_images/'.$item->user->image):null,'is_approved'=>(bool)$item->user->is_approved] : null,
            'reviewer' => $item->reviewer?->only(['id','name']),
        ];
        if ($details && $item->user) $data['user'] += ['address'=>$item->user->address,'member_since'=>$item->user->created_at?->toIso8601String()];
        return $data;
    }
}
