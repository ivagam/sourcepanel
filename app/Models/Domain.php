<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Notifications\Notifiable;

class Domain extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'domains';
    protected $primaryKey = 'domain_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'domain_name', 'created_by'
    ];
  
}