<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'product_id',
        'opening',
        'sales',
        'stock_value',
        'purchases',
        'damages',
        'closing',
        'stock_date',
    ];

    public $sortable = [
        'product_id',
        'opening',
        'sales',
        'stock_value',
        'purchases',
        'damages',
        'closing',
        'stock_date',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'product',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('sales', 'like', '%' . $search . '%');
        });
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
