<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

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
