<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'product_name',
        'qty',
        'price',
        'total_price',
    ];

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'order_id', 'id');
    }   

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
