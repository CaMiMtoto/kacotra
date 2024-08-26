<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

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
