<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_bn',
        'name_en' ,
        'thumbnail',
        'status',
        'created_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
