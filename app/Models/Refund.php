<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
