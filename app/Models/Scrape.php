<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Scrape extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'scrape';
    protected $primaryKey = 'scrape_id';
    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'url',
        'status',
        'created_at',
    ];

    public function products()
    {
        return $this->hasMany(ScrapeProduct::class, 'scrape_id', 'scrape_id');
    }
}
