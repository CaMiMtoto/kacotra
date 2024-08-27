<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Expense
 *
 * @property int $id
 * @property string $supplier_id
 * @property string $expense_date
 * @property string $expense_no
 * @property string|null $comment
 * @property string $expense_status 0=Pending, 1=Approved
 * @property int $total_amount
 * @property string $created_by
 * @property string|null $updated_by
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user_created
 * @property-read \App\Models\User|null $user_updated
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Expense extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'expense_date',
        'expense_no',
        'comment',
        'expense_status',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    public $sortable = [
        'expense_date',
        'total_amount',
    ];
    protected $guarded = [
        'id',
    ];

    protected $with = [
        'user_created',
        'user_updated',
    ];

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }

}
