<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
