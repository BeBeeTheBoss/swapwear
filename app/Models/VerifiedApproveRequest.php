<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedApproveRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nrc_front_image', 'nrc_back_image', 'status', 'reviewed_by', 'review_note', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
