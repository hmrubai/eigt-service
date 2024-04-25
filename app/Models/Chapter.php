<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_bn',
        'name_en' ,
        'category_id',
        'subject_id',
        'thumbnail',
        'status',
        'created_by',
        'deleted_at'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
