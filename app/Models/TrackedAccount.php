<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedAccount extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'bot_id',
        'account_id',
        'account_name',
        'notes',
        'notification_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function notificationUser()
    {
        return $this->belongsTo(User::class, 'notification_user_id');
    }

    public function trustedResources()
    {
        return $this->hasMany(TrustedResource::class);
    }
}
