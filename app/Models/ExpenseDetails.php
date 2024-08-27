<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExpenseDetails
 *
 * @property int $id
 * @property string $expense_id
 * @property string $issue_id
 * @property float $occurence
 * @property int $unitcost
 * @property float $total
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Expense|null $expense
 * @property-read \App\Models\Issue|null $issue
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereOccurence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereUnitcost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseDetails whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExpenseDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'expense_id',
        'issue_id',
        'occurence',
        'unitcost',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = ['issue'];

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }
}
