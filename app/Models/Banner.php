<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banner';
    protected $primaryKey = 'banner_id';

    protected $fillable = [
        'banner_name', 'domains', 'file_path', 'file_type', 'created_by',
    ];

     public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
