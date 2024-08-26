<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Method extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'slug',
        'code',
    ];

    protected $sortable = [
        'name',
        'slug',
        'code',
    ];

    protected $guarded = [
        'id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
