<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
