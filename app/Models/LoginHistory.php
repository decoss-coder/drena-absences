<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'succes', 'raison_echec', 'pays', 'ville', 'date_connexion'];
    protected $casts = ['date_connexion' => 'datetime', 'succes' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
