<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Issue
 *
 * @property int $id
 * @property string $issue_name
 * @property string $department_id
 * @property string $unit_id
 * @property string|null $issue_code
 * @property float $occurence
 * @property float|null $cost
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder|Issue filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereIssueCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereIssueName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereOccurence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Issue extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'issue_name',
        'department_id',
        'unit_id',
        'issue_code',
        'occurence',
        'cost',
    ];

    public $sortable = [
        'issue_name',
        'department_id',
        'unit_id',
        'issue_code',
        'occurence',
        'cost',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'department',
        'unit'
    ];

    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('issue_name', 'like', '%' . $search . '%');
        });
    }
}
