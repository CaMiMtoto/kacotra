<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Journal
 *
 * @property int $id
 * @property int $user_id
 * @property string $journal_date
 * @property string|null $description
 * @property string|null $reference
 * @property int $opening
 * @property int $debit
 * @property int $credit
 * @property int $due
 * @property int $refund
 * @property int $balance
 * @property string|null $comment
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Journal filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Journal sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereDebit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereJournalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereOpening($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Journal whereUserId($value)
 * @mixin \Eloquent
 */
class Journal extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'user_id',
        'journal_date',
        'description',
        'reference',
        'opening',
        'debit',
        'credit',
        'balance',
        'comment',
        'created_at',
        'updated_at'
    ];

    public $sortable = [
        'user_id',
        'journal_date',
        'description',
        'reference',
        'opening',
        'debit',
        'credit',
        'balance',
        'comment',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'user',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('description', 'like', '%' . $search . '%');
        });
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
