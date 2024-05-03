<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_bn',
        'name_en' ,
        'description',
        'category_id',
        'subject_id',
        'chapter_id',
        'raw_file',
        'transcoded_file_path',
        'compressed_file_path',
        'content_type',
        'thumbnail',
        'status',
        'created_by',
        'deleted_at'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
