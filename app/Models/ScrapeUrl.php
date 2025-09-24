<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ScrapeUrl extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'website_links';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'url',
        'anchor_text',
        'status',
        'product_status',
        'domain',
    ];
 
    public function products()
    {
        return $this->hasMany(ScrapeProduct::class, 'scrape_id', 'id');
    }
}
