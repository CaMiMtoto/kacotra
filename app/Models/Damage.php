<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Damage
 *
 * @property int $id
 * @property string $damage_date
 * @property string $damage_no
 * @property string $damage_status 0=Pending, 1=Approved
 * @property int $total_amount
 * @property string $created_by
 * @property string|null $updated_by
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user_created
 * @property-read \App\Models\User|null $user_updated
 * @method static \Illuminate\Database\Eloquent\Builder|Damage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Damage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Damage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Damage sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereDamageDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereDamageNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereDamageStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Damage whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Damage extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'damage_date',
        'damage_no',
        'damage_status',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    public $sortable = [
        'damage_date',
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

}
