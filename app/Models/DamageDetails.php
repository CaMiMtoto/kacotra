<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DamageDetails
 *
 * @property int $id
 * @property string $damage_id
 * @property string $product_id
 * @property float $quantity
 * @property int $unitcost
 * @property float $total
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Damage|null $damage
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereDamageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereUnitcost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageDetails whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DamageDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'damage_id',
        'product_id',
        'quantity',
        'unitcost',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function damage()
    {
        return $this->belongsTo(Damage::class, 'damage_id', 'id');
    }
}
