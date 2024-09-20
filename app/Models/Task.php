<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'end_date',
        'auth_id'
    ];

    public function auths()
    {
        return $this->belongsTo(Auth::class);
    }
}
