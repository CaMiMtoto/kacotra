<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Recovery
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $payment_type
 * @property int $pay
 * @property int $pay_cumul
 * @property string|null $pay_date
 * @property string|null $comment
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery query()
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery wherePay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery wherePayCumul($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery wherePayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Recovery whereUserId($value)
 * @mixin \Eloquent
 */
class Recovery extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_type',
        'pay',
        'pay_cumul',
        'pay_date',
        'comment',
        'created_at',
        'updated_at',
    ];

    public $sortable = [
        'order_id',
        'user_id',
        'payment_type',
        'pay',
        'pay_cumul',
        'pay_date',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'order',
        'user'
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('payment_type', 'like', '%' . $search . '%');
        });
    }
}
