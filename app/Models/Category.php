<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'category';
    protected $primaryKey = 'category_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'category_name', 'subcategory_id', 'alice_name', 'domains', 'created_by', 'category_ids'
    ];

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
    
    public function getDomains()
    {
        if (!$this->domains) {
            return [];
        }

        $domainIds = explode(',', $this->domains);
        return Domain::whereIn('domain_id', $domainIds)->get();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'subcategory_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'subcategory_id');
    }

    public function getFullPathAttribute()
    {
        $names = [];
        $category = $this;
        while ($category) {
            array_unshift($names, $category->category_name);
            $category = $category->parent;
        }
        return implode(' → ', $names);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

}