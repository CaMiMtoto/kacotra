<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_status',
        'is_confirmed',
        'total_products',
        'sub_total',
        'vat',
        'total',
        'invoice_no',
        'payment_type',
        'pay',
        'due',
    ];

    public $sortable = [
        'customer_id',
        'order_date',
        'pay',
        'due',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'customer',
        'user_created',
        'user_updated',
        'dues',
    ];
    protected $dates = ['from_date', 'to_date'];

    public static $rules = array(
        'fromDate' => 'regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/ | date_format:"Y-m-d"',
        'toDate' => 'regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/ | date_format:"Y-m-d"',
    );

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function dues() {
        return $this->hasMany(Due::class);
    }
/*
   static public function getDueOrders() {
        $dueOrders = DB::table('orders')
                        ->join('customers', 'orders.customer_id','=','customers.id')
                        ->select(
                            'customers.name as name',
                            'customers.phone as phone',
                            'orders.id as id',
                            'orders.order_date as order_date',
                            'orders.invoice_no as invoice_no',
                            'orders.payment_type as payment_type',
                            'orders.pay as pay',
                            'orders.due as due',
                            'orders.total as total'
                        )
                        ->where('orders.due', '>',0)
                        ->get();
        return $dueOrders;
    }
 */
    static public function getDueOrders() {
        $query = self::select(
            'orders*',
            'customers.name as name',
            'customers.phone as phone',
            )
            ->join('customers', 'orders.customer_id','=','customers.id')
            ->where('orders.due', '>',0)
            ->get();

        return $query;
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
