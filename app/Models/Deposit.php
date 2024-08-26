<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'method_id',
        'bank_id',
        'account_no',
        'deposit_date',
        'deposit_code',
        'transaction_id',
        'deposit_status',
        'amount',
        'created_by',
        'updated_by',
    ];

    public $sortable = [
        'method_id',
        'bank_id',
        'account_no',
        'deposit_date',
        'deposit_code',
        'transaction_id',
        'deposit_status',
        'amount',
        'created_by',
        'updated_by',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'method',
        'bank'
    ];

    public function method(){
        return $this->belongsTo(Method::class, 'method_id');
    }
    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('deposit_code', 'like', '%' . $search . '%');
        });
    }
}
