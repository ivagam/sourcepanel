<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatsapp extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_message';
    protected $primaryKey = 'id';

    protected $fillable = [
        'message', 'shortcut'
    ];

}
