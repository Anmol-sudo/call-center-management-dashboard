<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'extension',
        'department',
        'supervisor_id',
        'status',
        'idle_since',
    ];

    protected $casts = [
        'idle_since' => 'datetime',
    ];

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function callRecords()
    {
        return $this->hasMany(CallRecord::class);
    }

    public function phoneLines()
    {
        return $this->belongsToMany(PhoneLine::class, 'employee_phone_line');
    }
}

