<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';
    protected $primaryKey = 'media_id';

    protected $fillable = [
        'category_id', 'file_path', 'file_type', 'created_by', 'category_ids'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
