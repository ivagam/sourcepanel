<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ScrapeProduct extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'scrape_product';
    protected $primaryKey = 'scrape_product_id'; // Correct primary key name
    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'scrape_id',        
        'description',
        'product_name',
        'product_price',
        'category_id',
        'meta_keywords',
        'meta_description',
        'domains',
        'product_url',
        'created_by',
        'category_ids',
        'color',
        'size',
        'sku',
        'is_updated',
        'purchase_value',
        'purchase_code',
        'note',
        'is_product_c',
        'seo',
        'created_at',
        'updated_at'
    ];

    public function scrape()
    {
        return $this->belongsTo(Scrape::class, 'scrape_id', 'scrape_id');
    }

    public function images()
    {
        return $this->hasMany(ScrapeImage::class, 'scrape_product_id', 'scrape_product_id');
    }
}
