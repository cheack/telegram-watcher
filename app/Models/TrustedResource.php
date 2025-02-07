<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedResource extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'resource_id',
        'resource_name',
        'tracked_account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function trackedAccount()
    {
        return $this->belongsTo(TrackedAccount::class);
    }
}
