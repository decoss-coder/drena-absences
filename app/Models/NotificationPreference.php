<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'type_evenement', 'sms', 'email', 'push', 'in_app'];
    protected $casts = ['sms' => 'boolean', 'email' => 'boolean', 'push' => 'boolean', 'in_app' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
