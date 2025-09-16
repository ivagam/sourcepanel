<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ScrapeImage extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'scrape_images';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'scrape_product_id',
        'serial_no',
        'file_path',
        'created_by',
        'updated_at',
        'created_at',
    ];

    public function product()
    {
        return $this->belongsTo(ScrapeProduct::class, 'scrape_product_id', 'id');
    }
}
