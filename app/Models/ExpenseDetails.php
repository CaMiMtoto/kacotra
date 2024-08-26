<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
