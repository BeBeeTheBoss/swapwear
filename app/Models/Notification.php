<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notifications_user')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }
}
