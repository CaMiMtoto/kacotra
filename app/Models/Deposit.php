<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Deposit
 *
 * @property int $id
 * @property string $method_id
 * @property string $bank_id
 * @property string $account_no
 * @property string $deposit_date
 * @property string $deposit_code
 * @property string $transaction_id
 * @property string $deposit_status 0=Pending, 1=Approved
 * @property int $amount
 * @property string $created_by
 * @property string|null $updated_by
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Method|null $method
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereAccountNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereDepositCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereDepositDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereDepositStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereUpdatedBy($value)
 * @mixin \Eloquent
 */
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
