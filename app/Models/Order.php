<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $customer_id
 * @property string $order_date
 * @property string $order_status
 * @property int $is_confirmed
 * @property int $total_products
 * @property int $sub_total
 * @property int $vat
 * @property int $total
 * @property string $invoice_no
 * @property string $payment_type
 * @property int $pay
 * @property int $due
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer|null $customer
 * @property-read Collection<int, Due> $dues
 * @property-read int|null $dues_count
 * @property-read User|null $user_created
 * @property-read User|null $user_updated
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order sortable($defaultParameters = null)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCreatedBy($value)
 * @method static Builder|Order whereCustomerId($value)
 * @method static Builder|Order whereDue($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereInvoiceNo($value)
 * @method static Builder|Order whereIsConfirmed($value)
 * @method static Builder|Order whereIsDeleted($value)
 * @method static Builder|Order whereOrderDate($value)
 * @method static Builder|Order whereOrderStatus($value)
 * @method static Builder|Order wherePay($value)
 * @method static Builder|Order wherePaymentType($value)
 * @method static Builder|Order whereSubTotal($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereTotalProducts($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUpdatedBy($value)
 * @method static Builder|Order whereVat($value)
 * @mixin Eloquent
 */
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
    protected array $dates = ['from_date', 'to_date'];

    public static $rules = array(
        'fromDate' => 'regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/ | date_format:"Y-m-d"',
        'toDate' => 'regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/ | date_format:"Y-m-d"',
    );

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function dues()
    {
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
    static public function getDueOrders()
    {
        $query = self::select(
            'orders*',
            'customers.name as name',
            'customers.phone as phone',
        )
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.due', '>', 0)
            ->get();

        return $query;
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }
}
