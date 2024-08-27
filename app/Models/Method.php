<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Method
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $code
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Method filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Method newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Method newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Method query()
 * @method static \Illuminate\Database\Eloquent\Builder|Method sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Method whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
