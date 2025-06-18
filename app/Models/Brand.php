<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brand';
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_name', 'domains', 'file_path', 'file_type', 'created_by',
    ];

     public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
