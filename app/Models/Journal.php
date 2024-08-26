<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
