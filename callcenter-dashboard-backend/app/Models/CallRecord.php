<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRecord extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'phone_line_id',
        'direction',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
        'caller',
        'callee',
        'raw_cdr_id',
        'created_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function phoneLine()
    {
        return $this->belongsTo(PhoneLine::class);
    }
}

