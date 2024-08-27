<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Stock
 *
 * @property int $id
 * @property string|null $reference
 * @property int $product_id
 * @property float $opening
 * @property int $buying_price
 * @property int $stock_value
 * @property float $sales
 * @property int $sale_value
 * @property float $purchases
 * @property int $purchase_value
 * @property float $damages
 * @property int $damage_value
 * @property float $closing
 * @property int $closing_value
 * @property string|null $stock_date
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|Stock filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereBuyingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereClosing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereClosingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDamageValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDamages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereOpening($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock wherePurchaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock wherePurchases($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereSaleValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereStockDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereStockValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
