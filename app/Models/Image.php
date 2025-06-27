<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Notifications\Notifiable;

class Image extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'serial_no', 'product_id', 'file_path', 'created_by'
    ];

   public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

   
}