<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'description', 'status', 'priority'
    ];

    
}