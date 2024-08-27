<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Refund
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $payment_type
 * @property int $pay
 * @property string|null $refund_date
 * @property string|null $comment
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Refund filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund wherePay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUserId($value)
 * @mixin \Eloquent
 */
class Refund extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_type',
        'pay',
        'refund_date',
        'comment',
    ];

    public $sortable = [
        'order_id',
        'user_id',
        'payment_type',
        'pay',
        'refund_date',
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
