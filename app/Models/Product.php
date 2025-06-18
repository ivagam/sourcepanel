<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_name',
        'product_price',
        'category_id',
        'description',
        'created_by',
        'domains',
        'product_url',
        'meta_keywords',
        'meta_description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function getDomains()
    {
        if (!$this->domains) {
            return [];
        }

        $domainIds = explode(',', $this->domains);
        return Domain::whereIn('domain_id', $domainIds)->get();
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'product_id', 'product_id');
    }
}
