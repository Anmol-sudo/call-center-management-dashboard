<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PBXStatusSnapshot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'status',
        'pbx_version',
        'uptime_seconds',
        'active_calls',
        'registered_extensions',
        'total_extensions',
        'max_concurrent_calls',
        'snapshot_taken_at',
        'created_at',
    ];

    protected $casts = [
        'snapshot_taken_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}

