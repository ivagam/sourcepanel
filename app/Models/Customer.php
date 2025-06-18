<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Optional: allow mass assignment for these fields
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'zip_code',
    ];

    /**
     * A customer has many orders
     */
    public function orders()
    {
        return $this->hasMany(CustomerOrder::class, 'customer_id', 'id');
    }
}
