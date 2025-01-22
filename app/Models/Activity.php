<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracked_account_id',
        'activity_type',
        'details',
    ];

    public function trackedAccount()
    {
        return $this->belongsTo(TrackedAccount::class);
    }
}
