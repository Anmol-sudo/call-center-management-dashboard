<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'type',
        'status',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_phone_line');
    }
}

