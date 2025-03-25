<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUsers extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'google_id',
        'google_token',
    ];
}
