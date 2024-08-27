<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Due
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $customer
 * @property int $due
 * @property string|null $due_date
 * @property string|null $comment
 * @property int $due_status
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Due filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Due newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Due newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Due query()
 * @method static \Illuminate\Database\Eloquent\Builder|Due sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereDueStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Due whereUserId($value)
 * @mixin \Eloquent
 */
class Due extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'order_id',
        'user_id',
        'customer',
        'due',
        'due_date',
        'comment',
        'due_status',
        'created_at',
        'updated_at'
    ];

    public $sortable = [
        'order_id',
        'user_id',
        'customer',
        'due',
        'due_date',
        'due_status',
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
            return $query->where('comment', 'like', '%' . $search . '%');
        });
    }
}
