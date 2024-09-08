<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateCreateOrderRequest;
use App\Http\Requests\ValidatePayDueOrderRequest;
use App\Models\DamageDetails;
use App\Models\Deposit;
use App\Models\Due;
use App\Models\ExpenseDetails;
use App\Models\Journal;
use App\Models\Method;
use App\Models\PurchaseDetails;
use App\Models\Recovery;
use App\Models\Refund;
use App\Models\Stock;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Throwable;

class OrderController extends Controller
{
    /**
     * Display a pending orders.
     */
    public function pendingOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('order_status', 'pending')
            ->where('is_confirmed', 0)
            ->where('orders.is_deleted', 0)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display a deleted orders.
     */
    public function deletedOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('orders.is_deleted', 1)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display a pending orders.
     */
    public function completeOrders()
    {
        $row = (int)request('row', 100);
        /* $from = Carbon::createFromFormat('Y-m-d', request('fromDate')) ;
        $to = Carbon::createFromFormat('Y-m-d', request('toDate')); */

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('order_status', 'complete')
            ->where('orders.is_deleted', 0)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    public function dueOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('due', '>', 0)
            ->where('orders.is_deleted', 0)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display due order details.
     */
    public function dueOrderDetails(string $order_id)
    {

        $order = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
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
            ->where('orders.id', $order_id)
            ->where('orders.is_deleted', 0)
            ->first();

        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('products.is_deleted', 0)
            ->select(
                'products.product_name as product_name',
                'products.product_code as product_code',
                'products.stock as stock',
                'products.buying_price as buying_price',
                'products.selling_price as selling_price',
                'order_details.id as id',
                'order_details.order_id as order_id',
                'order_details.quantity as quantity',
                'order_details.unitcost as unitcost',
                'order_details.total as total',
                'order_details.created_at as created_at',
                'order_details.updated_at as updated_at'
            )
            ->where('order_id', $order_id)
            ->where('order_details.is_deleted', 0)
            ->orderBy('id')
            ->get();

        return view('orders.details-due-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'methods' => Method::where('is_deleted', 0)->get(),
        ]);
    }

    public function refundOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('due', '<', '0')
            ->where('orders.is_deleted', 0)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due'); // Refund

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay') - $cashDue;
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver, /* Refund */
        ]);
    }

    /**
     * Display refund order details.
     */
    public function refundOrderDetails(string $order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('is_deleted', 0)
            ->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->where('is_deleted', 0)
            ->orderBy('id')
            ->get();

        return view('orders.details-refund-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'methods' => Method::where('is_deleted', 0)->get(),
        ]);
    }

    /**
     * Display an order details.
     */
    public function orderDetails(string $order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('is_deleted', 0)
            ->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->where('is_deleted', 0)
            ->orderBy('id')
            ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Display a order details.
     */
    public function orderReportDetails(string $order_id)
    {
        $order = Order::with(['customer', 'user_created', 'user_updated'])
            ->where('id', $order_id)
            ->where('is_deleted', 0)
            ->first();

        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->where('is_deleted', 0)
            ->orderBy('id')
            ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Handle create new order
     * @throws Throwable
     */
    public function createOrder(ValidateCreateOrderRequest $request)
    {
        $validatedData = $request->validated();

        DB::transaction(function () use ($validatedData) {
            // Generate Invoice Number
            $invoice_no = IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]);

            // Prepare Order Data
            $validatedData = array_merge($validatedData, [
                'order_date' => Carbon::now()->format('Y-m-d'),
                'order_status' => 'pending',
                'total_products' => Cart::count(),
                'sub_total' => Cart::subtotal(),
                'vat' => Cart::tax(),
                'invoice_no' => $invoice_no,
                'total' => Cart::total(),
                'due' => ((int)Cart::total() - (int)$validatedData['pay']),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_confirmed' => 1,
            ]);

            // Insert Order and get ID
            $order = Order::query()->create($validatedData);

            // Prepare and Insert Order Details
            $contents = Cart::content();
            $orderDetails = [];

            foreach ($contents as $content) {
                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->subtotal,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            OrderDetails::insert($orderDetails);

            // Update Product Stock
            foreach ($contents as $content) {
                Product::where('id', $content->id)
                    ->where('is_deleted', 0)
                    ->decrement('stock', $content->qty);
            }

            // Clear Cart
            Cart::destroy();
        });

        return Redirect::route('order.pendingOrders')->with('success', 'Order has been created!');
    }

    /**
     * Handle update a status order
     */
    public function updateOrder(Request $request)
    {
        $order_id = $request->id;

        $order = Order::where('id', $order_id)
            ->where('is_deleted', 0)
            ->first();

        $reference = $order->invoice_no;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)
            ->where('is_deleted', 0)
            ->get();

        foreach ($products as $product) {
            $isOpened = Stock::where('product_id', $product->product_id)
                ->where('is_deleted', 0)
                ->whereDate('stock_date', Carbon::now())
                ->first();

            if ((empty($isOpened) || $isOpened == null) && $order->is_confirmed == 0) {
                $opening = Product::where('id', $product->product_id)
                    ->where('is_deleted', 0)
                    ->first();
                if (!empty($opening->stock)) {
                    Stock::insert([
                        'reference' => $reference,
                        'product_id' => $product->product_id,
                        'opening' => $opening->stock,
                        'buying_price' => $opening->buying_price,
                        'stock_value' => $opening->stock * $opening->selling_price,
                        'sales' => $product->quantity,
                        'sale_value' => $product->total,
                        'purchases' => 0,
                        'purchase_value' => 0,
                        'damages' => 0,
                        'damage_value' => 0,
                        'stock_date' => Carbon::now()->format('Y-m-d'),
                        'closing' => $opening->stock - $product->quantity,
                        'closing_value' => ($opening->stock - $product->quantity) * $opening->selling_price,
                        'created_at' => Carbon::now()
                    ]);
                }
            } else {
                if ($order->is_confirmed == 0) {
                    $currentStock = Stock::where('product_id', $product->product_id)
                        ->where('is_deleted', 0)
                        ->whereDate('stock_date', Carbon::now())
                        ->first();

                    $c_product = Product::where('id', $product->product_id)
                        ->where('is_deleted', 0)
                        ->first();

                    Stock::where('product_id', $product->product_id)
                        ->where('is_deleted', 0)
                        ->whereDate('stock_date', Carbon::now())
                        ->update([
                            'sales' => $currentStock->sales + $product->quantity,
                            'sale_value' => $currentStock->sale_value + $product->total,
                            'closing' => $currentStock->closing - $product->quantity,
                            'closing_value' => ($currentStock->closing - $product->quantity) * $c_product->buying_price,
                        ]);
                }
            }

            Product::where('id', $product->product_id)
                ->where('is_deleted', 0)
                ->update(['stock' => DB::raw('stock-' . $product->quantity)]);
        }

        Order::findOrFail($order_id)->update([
            'order_status' => 'complete',
            'is_confirmed' => 1,
            'updated_by' => auth()->user()->id
        ]);

        if ($order->due > 0) {
            $comment = "Invoice paid partially";
            Order::findOrFail($order_id)->update([
                'order_status' => 'pending',
                'is_confirmed' => 1
            ]);

            Due::insert([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'due' => $order->due,
                'due_date' => Carbon::now()->format('Y-m-d'),
                'comment' => $comment,
                'created_at' => Carbon::now()
            ]);
        }

        if ($order->due < 0) {
            Order::findOrFail($order_id)->update([
                'order_status' => 'refund',
                'is_confirmed' => 1
            ]);
        }

        if ($order->payment_type == 'Due') {
            $due = $order->pay;
            $comment = "Invoice not paid!";
            $pay = 0;
            Order::findOrFail($order_id)->update([
                'order_status' => 'pending',
                'is_confirmed' => 1,
                'pay' => $pay,
                'due' => $due
            ]);

            Due::insert([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'due' => $order->pay,
                'due_date' => Carbon::now()->format('Y-m-d'),
                'comment' => $comment,
                'created_at' => Carbon::now()
            ]);
        }

        /**
         * Record sales to journal
         */

        $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
            ->where('is_deleted', 0)
            ->first();

        $journals = Journal::where('is_deleted', 0)
            ->get();

        $debit = $order->pay;
        $credit = 0;
        $due = 0;
        $refund = 0;
        $comment = $order->payment_type;
        $customer = $order->customer->name;
        if ($order->payment_type == 'Due') {
            $debit = 0;
            $due = $order->pay;
            $comment = "Ideni rya " . $customer . " rihwanye na " . number_format($order->pay) . " Rwf.";
        }

        if ($order->due > 0) {
            $due = $order->due;
            $comment = $customer . " yishyuye igice asigaramo ideni rya " . number_format($order->due) . " Rwf.";
        }

        if ($order->due < 0) {
            $refund = $order->due;
            $comment = $customer . " yishyuye " . number_format(-$refund) . " Rwf arenga kuyo yagombaga kwishyura!";
        }


        if (empty($journal) || $journal == null) {
            if ($journals->count() == 0) {
                $opening_value = 0;
                $balance = $debit;
            } else {
                $opening_value = $journals[$journals->count() - 1]->balance;
                $balance = $opening_value + $debit;
            }
        } else {
            $opening_value = $journal->opening;
            $balance = $journals[$journals->count() - 1]->balance + $debit;
        }
        $description = "Sales";
        $user = auth()->user()->id;
        Journal::insert([
            'user_id' => $user,
            'journal_date' => Carbon::now()->format('Y-m-d'),
            'description' => $description,
            'reference' => $reference,
            'opening' => $opening_value,
            'debit' => $debit,
            'credit' => $credit,
            'due' => $due,
            'refund' => $refund,
            'balance' => $balance,
            'comment' => $comment,
            'created_at' => Carbon::now()->format('Y-m-d'),
        ]);

        return Redirect::route('order.completeOrders')->with('success', 'Order has been completed!');
    }

    /**
     * Handle update a due pay order
     */
    public function updateDueOrder(Request $request)
    {

        $rules = [
            'id' => 'required|numeric',
            'pay' => 'required|numeric',
            'payment_type' => 'required',
            'comment' => 'nullable|string|max:191'
        ];

        if (auth()->user()->role == 'seller') {

            $validatedData = $request->validate($rules);
            $orderDue = Order::findOrFail($validatedData['id']);
            $due_status = 0;

            $mainPay = $orderDue->pay;
            $mainDue = $orderDue->due;

            $paidDue = $mainDue - $validatedData['pay'];
            $paidPay = $mainPay + $validatedData['pay'];

            Order::findOrFail($validatedData['id'])->update([
                'due' => $paidDue,
                'pay' => $paidPay
            ]);
            /*
            $order = Order::where('id',$validatedData['id'])->first();
            $customer = $order->customer->name;
            */
            /*
            $order = DB::table('orders')
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
                        ->where('orders.id', $validatedData['id'])
                        ->first();
             */

            $due_order = new Order;

            $order = $due_order->getDueOrders()->where('id', $validatedData['id'])->first();
            // $order = Order::with('customer')->where('id',$validatedData['id'])->first();

            $comment = "Pending invoice!";
            $customer = $order->customer->name;

            if ($order->due <= 0) {
                $due_status = 1;
                $comment = "Invoice cleared!";
                Order::findOrFail($validatedData['id'])->update([
                    'order_status' => 'complete'
                ]);
            }

            Due::insert([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'customer' => $customer,
                'due' => $paidDue,
                'due_date' => Carbon::now()->format('Y-m-d'),
                'due_status' => $due_status,
                'comment' => $comment,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            Recovery::insert([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'payment_type' => $validatedData['payment_type'],
                'pay' => $validatedData['pay'],
                'pay_cumul' => $paidPay,
                'pay_date' => Carbon::now()->format('Y-m-d'),
                'comment' => $validatedData['comment'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            /**
             * Record due payment to journal
             */

            $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
                ->where('is_deleted', 0)
                ->first();

            $journals = Journal::where('is_deleted', 0)->get();

            $debit = $validatedData['pay'];
            $credit = 0;
            $due = 0;
            $refund = 0;
            $comment = $customer . " yishyuye umwenda wari warasigaye ubwo yishyuraga!";

            if ($order->due > 0) {
                $due = $order->due;
                $comment = $customer . " yishyuye igice asigaramo ideni rya " . number_format($order->due) . " Rwf.";
            }

            if ($order->due < 0) {
                $refund = $order->due;
                $comment = $customer . " yishyuye " . number_format($order->due) . " Rwf arenga kuyo yagombaga kutwishyura!";
            }

            if (empty($journal) || $journal == null) {
                if ($journals->count() == 0) {
                    $opening_value = 0;
                    $balance = $debit;
                } else {
                    $opening_value = $journals[$journals->count() - 1]->balance;
                    $balance = $opening_value + $debit;
                }
            } else {
                $opening_value = $journal->opening;
                $balance = $journals[$journals->count() - 1]->balance + $debit;
            }
            $description = "Payment of due invoice";
            $reference = $order->invoice_no;
            $user = auth()->user()->id;
            Journal::insert([
                'user_id' => $user,
                'journal_date' => Carbon::now()->format('Y-m-d'),
                'description' => $description,
                'reference' => $reference,
                'opening' => $opening_value,
                'debit' => $debit,
                'credit' => $credit,
                'due' => $due,
                'refund' => $refund,
                'balance' => $balance,
                'comment' => $comment,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return Redirect::route('order.dueOrders')->with('success', 'Due amount has been updated!');
        } else {
            return Redirect::route('order.dueOrders')->with('error', 'Sorry, you don\'t have enough permission, contact the admin!');
        }
    }

    public function updateOrderDue($id, Request $request)
    {
        request()->validate([
            'id' => 'required|numeric',
            'pay' => 'required|numeric',
            'payment_type' => 'required',
            'comment' => 'nullable|string|max:191'
        ]);

        $order = Order::getSingle($id);
        $due_status = 0;

        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paidDue = $mainDue - $request->pay;
        $paidPay = $mainPay + $request->pay;

        $order->due = $paidDue;
        $order->pay = $paidPay;
        $order->save();

        // $due_order = Order::getDueOrders()->where('id','=',$request->id)->first();
        $comment = "Pending invoice!";
        $customer = $order->name;

        if ($order->due <= 0) {
            $due_status = 1;
            $comment = "Invoice cleared!";
            $order->order_status = 'complete';
            $order->save();
        }
        /*
        $due = new Due;
        $due -> order_id = $order->id;
        $due -> user_id = auth()->user()->id;
        $due -> customer = $customer;
        $due -> due = $paidDue;
        $due -> due_date = Carbon::now()->format('Y-m-d');
        $due -> due_status = $due_status;
        $due -> comment = $comment;
        $due -> created_at = Carbon::now();
        $due -> updated_at = Carbon::now();
        $due -> save();
         */
        /*
        Due::insert([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'customer' => $customer,
            'due' => $paidDue,
            'due_date' =>Carbon::now()->format('Y-m-d'),
            'due_status' => $due_status,
            'comment' => $comment,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
         */
        return Redirect::route('order.dueOrders')->with('success', 'Due amount has been updated!');
    }

    /**
     * Handle update an overdue pay order
     */
    public function updateRefundOrder(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'pay' => 'required|numeric',
            'payment_type' => 'required',
            'comment' => 'nullable|string|max:191'
        ];

        $validatedData = $request->validate($rules);
        $order = Order::findOrFail($validatedData['id']);

        $credit = $validatedData['pay'];

        // $mainPay = $order->pay;
        $mainDue = $order->due;

        $paidDue = $mainDue + $validatedData['pay'];
        // $paidPay = $mainPay + $validatedData['pay'];

        Order::findOrFail($validatedData['id'])->update([
            'due' => $paidDue,

        ]);

        $order = Order::where('id', $validatedData['id'])
            ->where('is_deleted', 0)
            ->first();
        $customer = $order->customer->name;

        if ($order->due == 0) {
            Order::findOrFail($validatedData['id'])->update([
                'order_status' => 'complete'
            ]);
        } elseif ($order->due < 0) {
            Order::findOrFail($validatedData['id'])->update([
                'order_status' => 'refund'
            ]);
        } else {
            Order::findOrFail($validatedData['id'])->update([
                'order_status' => 'pending'
            ]);
        }

        Refund::insert([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'payment_type' => $validatedData['payment_type'],
            'pay' => $validatedData['pay'],
            'refund_date' => Carbon::now()->format('Y-m-d'),
            'comment' => $validatedData['comment'],
            'created_at' => Carbon::now()
        ]);

        /**
         * Record refund to journal
         */

        $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
            ->where('is_deleted', 0)
            ->first();

        $journals = Journal::where('is_deleted', 0)->get();

        $debit = 0;
        $credit = $validatedData['pay'];
        $due = 0;
        $refund = 0;
        if ($paidDue == 0) {
            $comment = $customer . " yasubijwe amafaranga yose (" . number_format($mainDue) . " Rwf) yarenganga kuyo yagombaga
            kwishyura!";
        }
        if ($paidDue > 0) {
            $due = $paidDue;
            $comment = $customer . " yasubijwe amafaranga menshi, " . number_format($credit) . " Rwf, atugiyemo ideni rya
            " . number_format($paidDue) . " Rwf !";
        }

        if ($paidDue < 0) {
            $refund = $paidDue;
            $comment = $customer . " yasubijwe amafaranga " . number_format($credit) . " Rwf dusigaje kumusubiza " .
                number_format($paidDue) . " Rwr.";
        }

        if (empty($journal) || $journal == null) {
            if ($journals->count() == 0) {
                $opening_value = 0;
                $balance = -$credit;
            } else {
                $opening_value = $journals[$journals->count() - 1]->balance;
                $balance = $opening_value - $credit;
            }
        } else {
            $opening_value = $journal->opening;
            $balance = $journals[$journals->count() - 1]->balance - $credit;
        }
        $description = "Refund of overdue invoice";
        $reference = $order->invoice_no;
        $user = auth()->user()->id;
        Journal::insert([
            'user_id' => $user,
            'journal_date' => Carbon::now()->format('Y-m-d'),
            'description' => $description,
            'reference' => $reference,
            'opening' => $opening_value,
            'debit' => $debit,
            'credit' => $credit,
            'due' => $due,
            'refund' => $refund,
            'balance' => $balance,
            'comment' => $comment,
            'created_at' => Carbon::now()->format('Y-m-d'),
        ]);

        return Redirect::route('order.refundOrders')->with('success', 'Refund amount has been updated!');
    }

    /**
     * @param ValidatePayDueOrderRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function payDueOrder(ValidatePayDueOrderRequest $request)
    {
        $validatedData = $request->validated();
        $order = Order::findOrFail($validatedData['id']);

        $amountToPay = $validatedData['pay'];
        $previousDue = $order->due;
        $amountDue = $previousDue - $amountToPay;
        $totalPaid = $order->pay + $amountToPay;

        if ($amountDue < 0) {
            return Redirect::route('order.dueOrderDetails', $order->id)
                ->with('error', 'You cannot pay more than the due amount!');
        }
        DB::beginTransaction();
        try {
            // Determine the new order status
            $newStatus = $amountDue == 0 ? 'complete' : ($amountDue > 0 ? 'pending' : 'refund');

            // Update order with the new values
            $order->update([
                'pay' => $totalPaid,
                'due' => $amountDue,
                'order_status' => $newStatus,
            ]);

            // Record due in journal
            $this->recordJournalEntry($order, $amountToPay, $amountDue);
            // save order payment
            $paymentType = Method::query()->where('code', '=', $validatedData['payment_type'])->firstOrFail();
            $order->payments()->create([
                'payment_type' => $paymentType->name,
                'method_id' => $paymentType->id,
                'pay' => $amountToPay,
                'due' => $amountDue,
                'payment_date' => \request('payment_date', Carbon::now()->format('Y-m-d')),
                'comment' => $validatedData['comment'],
            ]);

            DB::commit();
            return Redirect::route('order.dueOrders')->with('success', 'Due amount has been updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::route('order.dueOrderDetails', $order->id)
                ->with('error', 'An error occurred while processing the payment!: ' . $e->getMessage());
        }


    }

    private function recordJournalEntry($order, $debit, $amountDue)
    {
        $customer = $order->customer->name;
        $description = "Refund of overdue invoice";
        $reference = $order->invoice_no;
        $user = auth()->user()->id;

        // Define comment based on due amount
        if ($amountDue == 0) {
            $comment = "{$customer} yishyuye amafaranga yose (" . number_format($debit) . " Rwf)!";
        } elseif ($amountDue > 0) {
            $comment = "{$customer} yishyuye amafaranga menshi, " . number_format($debit) . " Rwf, tugomba kumusubiza " . number_format($amountDue) . " Rwf!";
        } else {
            $comment = "{$customer} yishyuye amafaranga " . number_format($debit) . " Rwf atugiyemo ideni rya " . number_format($amountDue) . " Rwf.";
        }

        $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
            ->where('is_deleted', 0)
            ->first();

        $journals = Journal::where('is_deleted', 0)->get();

        if (!$journal) {
            $opening_value = $journals->count() ? $journals->last()->balance : 0;
            $balance = $opening_value + $debit;
        } else {
            $opening_value = $journal->opening;
            $balance = $journal->balance + $debit;
        }

        Journal::create([
            'user_id' => $user,
            'journal_date' => Carbon::now()->format('Y-m-d'),
            'description' => $description,
            'reference' => $reference,
            'opening' => $opening_value,
            'debit' => $debit,
            'credit' => 0,
            'due' => max(0, $amountDue),
            'refund' => min(0, $amountDue),
            'balance' => $balance,
            'comment' => $comment,
            'created_at' => Carbon::now()->format('Y-m-d'),
        ]);
    }

    /**
     * Handle to print an invoice.
     */
    public function downloadInvoice(int $order_id)
    {
        $order = Order::with('customer')
            ->where('id', $order_id)
            ->where('is_deleted', 0)
            ->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC')
            ->get();

        return view('orders.print-invoice', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Display an all orders.
     */
    public function dailyOrderReport()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::with(['customer'])
            ->where('order_date', Carbon::now()->format('Y-m-d')) // 1 = approved
            ->where('is_deleted', 0)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('payment_type', '=', 'Due')->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display an all orders.
     */
    public function dailyProductOrderReport()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('stocks', 'products.id', '=', 'stocks.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->where('stocks.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                'order_details.created_at as o_date',
                'stocks.opening as p_open',
                'stocks.buying_price as p_buying',
                'stocks.stock_value as p_stock',
                'stocks.closing as p_close',
                'stocks.closing_value as p_close_value',
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->whereDate('order_details.created_at', '=', Carbon::now()->format('Y-m-d'))
            ->whereDate('stocks.stock_date', '=', Carbon::now()->format('Y-m-d'))
            ->where('products.is_deleted', 0)
            ->groupBy('p_name')
            ->paginate($row)
            ->appends(request()->query());
        $title = "Sales of " . Carbon::now()->format('M d, Y');

        return view('products.sales', [
            'products' => $products,
            'title' => $title,
        ]);
    }

    /**
     * Display today cash flow.
     */
    public function dailyCashReport()
    {

        $from_date = \request('from_date', Carbon::now()->format('Y-m-d'));
        $to_date = \request('to_date', Carbon::now()->format('Y-m-d'));
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $sales = $this->getSales($row);

        $total_paid = $this->getTotalPaid($from_date, $to_date);
        $total_due = $this->getTotalDue($from_date, $to_date);

        $total_refund = $this->getTotalRefund($from_date, $to_date);


        $title = "Cash flow of " . $from_date . " to " . $to_date;

        if ($from_date == $to_date) {
            $title = "Cash flow of " . $from_date;
        } else if (Carbon::parse($from_date)->diffInYears(Carbon::parse($to_date)) == 1) {
            $title = "Annual cash flow of " . $from_date . " to " . $to_date;
        } else if (Carbon::parse($from_date)->diffInMonths(Carbon::parse($to_date)) == 1) {
            $title = "Monthly cash flow of " . $from_date . " to " . $to_date;
        } else if (Carbon::parse($from_date)->diffInWeeks(Carbon::parse($to_date)) == 1) {
            $title = "Weekly cash flow of " . $from_date . " to " . $to_date;
        }


        $subTotal1 = $this->getSalesValue($from_date, $to_date);
        $purchases = $this->getPurchases($row);
        $subTotal2 = $this->getPurchasesValue($from_date, $to_date);
        $expenses = $this->getExpenses($row);
        $subTotal3 = $this->getTotalExpenses($from_date, $to_date);
        $deposits = $this->getDeposits($row);
        $subTotal4 = $this->getTotalDeposits($from_date, $to_date);
        $damages = $this->getDamages($row);

        $subTotal5 = $this->getTotalDamages($from_date, $to_date);
        $recoveries = $this->getRecoveries($row);
        $subTotal6 = $this->getTotalRecoveries($from_date, $to_date);
        $refunds = $this->getRefunds($row);

        $subTotal7 = $this->getTotalRefunds($from_date, $to_date);
        $dues = $this->getDues($row);
        $subTotal8 = $this->getTotalDues($from_date, $to_date);
        $total = $total_paid - $subTotal7 - $subTotal3 - $subTotal4 + $subTotal6;

        return view('products.cashflow', [
            'sales' => $sales,
            'title' => $title,
            'subTotal1' => $subTotal1,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'total_refund' => $total_refund,
            'purchases' => $purchases,
            'subTotal2' => $subTotal2,
            'expenses' => $expenses,
            'subTotal3' => $subTotal3,
            'deposits' => $deposits,
            'subTotal4' => $subTotal4,
            'damages' => $damages,
            'subTotal5' => $subTotal5,
            'recoveries' => $recoveries,
            'subTotal6' => $subTotal6,
            'refunds' => $refunds,
            'subTotal7' => $subTotal7,
            'dues' => $dues,
            'subTotal8' => $subTotal8,
            'total' => $total,
        ]);

    }

    /**
     * Display current week cash flow.
     */
    public function currentWeekCashReport()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        $sales = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                'order_details.created_at as o_date',
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->whereBetween('order_details.created_at', [$weekStartDate, $weekEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('p_name')
            ->paginate($row)
            ->appends(request()->query());

        $title = "Cashflow from " . $weekStartDate . " to " . $weekEndDate;

        $subTotal1 = OrderDetails::with('product')
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $total_paid = Order::whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');
        $total_due = Order::whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');
        $total_refund = Order::whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('due', '<', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $purchases = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('purchase_details.is_deleted', 0)
            ->select(
                'products.product_name as pur_name',
                'purchase_details.product_id as pur_id',
                'purchase_details.created_at as pur_date',
                DB::raw('SUM(purchase_details.quantity) as pur_quantity'),
                DB::raw('SUM(purchase_details.total) as pur_total')
            )
            ->whereBetween('purchase_details.created_at', [$weekStartDate, $weekEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('pur_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal2 = PurchaseDetails::with('product')
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $expenses = DB::table('issues')
            ->join('departments', 'issues.department_id', '=', 'departments.id')
            ->join('units', 'issues.unit_id', '=', 'units.id')
            ->join('expense_details', 'issues.id', '=', 'expense_details.issue_id')
            ->where('departments.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('expense_details.is_deleted', 0)
            ->select(
                'issues.issue_name as exp_name',
                'expense_details.issue_id as exp_id',
                'expense_details.created_at as exp_date',
                DB::raw('SUM(expense_details.occurence) as exp_quantity'),
                DB::raw('SUM(expense_details.total) as exp_total')
            )
            ->whereBetween('expense_details.created_at', [$weekStartDate, $weekEndDate])
            ->where('issues.is_deleted', 0)
            ->groupBy('exp_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal3 = ExpenseDetails::with('issue')
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $deposits = DB::table('deposits')
            ->join('banks', 'deposits.bank_id', '=', 'banks.id')
            ->where('banks.is_deleted', 0)
            ->select(
                'deposits.deposit_code as dep_code',
                'banks.name as dep_name',
                DB::raw('SUM(deposits.amount) as dep_total')
            )
            ->whereBetween('deposits.created_at', [$weekStartDate, $weekEndDate])
            ->where('deposits.is_deleted', 0)
            ->groupBy('deposits.bank_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal4 = Deposit::with('bank')
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('amount');

        $damages = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('damage_details', 'products.id', '=', 'damage_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('damage_details.is_deleted', 0)
            ->select(
                'products.product_name as dam_name',
                'damage_details.product_id as dam_id',
                'damage_details.created_at as dam_date',
                DB::raw('SUM(damage_details.quantity) as dam_quantity'),
                DB::raw('SUM(damage_details.total) as dam_total')
            )
            ->whereBetween('damage_details.created_at', [$weekStartDate, $weekEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('dam_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal5 = DamageDetails::with('product')
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $recoveries = DB::table('recoveries')
            ->join('methods', 'recoveries.payment_type', '=', 'methods.code')
            ->join('orders', 'recoveries.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as rec_method',
                'methods.code as rec_code',
                'customers.name as rec_customer',
                'orders.invoice_no as rec_invoice',
                'recoveries.payment_type as rec_pay_type',
                'recoveries.pay_date as rec_date',
                DB::raw('SUM(recoveries.pay) as rec_total')
            )
            ->whereBetween('recoveries.pay_date', [$weekStartDate, $weekEndDate])
            ->where('recoveries.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal6 = Recovery::with('order')
            ->whereBetween('pay_date', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $refunds = DB::table('refunds')
            ->join('methods', 'refunds.payment_type', '=', 'methods.code')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as ref_method',
                'methods.code as ref_code',
                'customers.name as ref_customer',
                'orders.invoice_no as ref_invoice',
                'refunds.payment_type as ref_pay_type',
                'refunds.refund_date as ref_date',
                DB::raw('SUM(refunds.pay) as ref_total')
            )
            ->whereBetween('refunds.refund_date', [$weekStartDate, $weekEndDate])
            ->where('refunds.is_deleted', 0)
            ->groupBy('refunds.order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal7 = Refund::with('order')
            ->whereBetween('refund_date', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $dues = DB::table('dues')
            ->join('orders', 'dues.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'orders.invoice_no as due_invoice',
                'customers.name as due_customer',
                'dues.due_date as due_date',
                'dues.comment as due_comment',
                DB::raw('SUM(dues.due) as due_total')
            )
            ->whereBetween('dues.due_date', [$weekStartDate, $weekEndDate])
            ->where('dues.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());
        /*
        $subTotal8 = Due::with('order')
            ->whereBetween('due_date',[$weekStartDate, $weekEndDate])
            -> sum('due');
         */
        $subTotal8 = Order::whereBetween('updated_at', [$weekStartDate, $weekEndDate])
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $total = $total_paid - $subTotal7 - $subTotal3 - $subTotal4 + $subTotal6;

        return view('products.cashflow', [
            'sales' => $sales,
            'title' => $title,
            'subTotal1' => $subTotal1,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'total_refund' => $total_refund,
            'purchases' => $purchases,
            'subTotal2' => $subTotal2,
            'expenses' => $expenses,
            'subTotal3' => $subTotal3,
            'deposits' => $deposits,
            'subTotal4' => $subTotal4,
            'damages' => $damages,
            'subTotal5' => $subTotal5,
            'recoveries' => $recoveries,
            'subTotal6' => $subTotal6,
            'refunds' => $refunds,
            'subTotal7' => $subTotal7,
            'dues' => $dues,
            'subTotal8' => $subTotal8,
            'total' => $total,
        ]);

    }


    /**
     * Display current week cash flow.
     */
    public function currentMonthCashReport()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $now = Carbon::now();
        $monthStartDate = $now->startOfMonth()->format('Y-m-d');
        $monthEndDate = $now->endOfMonth()->format('Y-m-d');

        $sales = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                'order_details.created_at as o_date',
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->whereBetween('order_details.created_at', [$monthStartDate, $monthEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('p_name')
            ->paginate($row)
            ->appends(request()->query());

        $title = "Cashflow of " . Carbon::now()->format('M, Y');

        $subTotal1 = OrderDetails::with('product')
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $total_paid = Order::whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');
        $total_due = Order::whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->where('due', '>', 0)
            ->sum('due');
        $total_refund = Order::whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->where('due', '<', 0)
            ->sum('due');

        $purchases = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('purchase_details.is_deleted', 0)
            ->select(
                'products.product_name as pur_name',
                'purchase_details.product_id as pur_id',
                'purchase_details.created_at as pur_date',
                DB::raw('SUM(purchase_details.quantity) as pur_quantity'),
                DB::raw('SUM(purchase_details.total) as pur_total')
            )
            ->whereBetween('purchase_details.created_at', [$monthStartDate, $monthEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('pur_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal2 = PurchaseDetails::with('product')
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $expenses = DB::table('issues')
            ->join('departments', 'issues.department_id', '=', 'departments.id')
            ->join('units', 'issues.unit_id', '=', 'units.id')
            ->join('expense_details', 'issues.id', '=', 'expense_details.issue_id')
            ->where('departments.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('expense_details.is_deleted', 0)
            ->select(
                'issues.issue_name as exp_name',
                'expense_details.issue_id as exp_id',
                'expense_details.created_at as exp_date',
                DB::raw('SUM(expense_details.occurence) as exp_quantity'),
                DB::raw('SUM(expense_details.total) as exp_total')
            )
            ->whereBetween('expense_details.created_at', [$monthStartDate, $monthEndDate])
            ->where('issues.is_deleted', 0)
            ->groupBy('exp_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal3 = ExpenseDetails::with('issue')
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $deposits = DB::table('deposits')
            ->join('banks', 'deposits.bank_id', '=', 'banks.id')
            ->where('banks.is_deleted', 0)
            ->select(
                'deposits.deposit_code as dep_code',
                'banks.name as dep_name',
                DB::raw('SUM(deposits.amount) as dep_total')
            )
            ->whereBetween('deposits.created_at', [$monthStartDate, $monthEndDate])
            ->where('deposits.is_deleted', 0)
            ->groupBy('deposits.bank_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal4 = Deposit::with('bank')
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('amount');

        $damages = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('damage_details', 'products.id', '=', 'damage_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('damage_details.is_deleted', 0)
            ->select(
                'products.product_name as dam_name',
                'damage_details.product_id as dam_id',
                'damage_details.created_at as dam_date',
                DB::raw('SUM(damage_details.quantity) as dam_quantity'),
                DB::raw('SUM(damage_details.total) as dam_total')
            )
            ->whereBetween('damage_details.created_at', [$monthStartDate, $monthEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('dam_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal5 = DamageDetails::with('product')
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $recoveries = DB::table('recoveries')
            ->join('methods', 'recoveries.payment_type', '=', 'methods.code')
            ->join('orders', 'recoveries.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as rec_method',
                'methods.code as rec_code',
                'customers.name as rec_customer',
                'orders.invoice_no as rec_invoice',
                'recoveries.payment_type as rec_pay_type',
                'recoveries.pay_date as rec_date',
                DB::raw('SUM(recoveries.pay) as rec_total')
            )
            ->whereBetween('recoveries.pay_date', [$monthStartDate, $monthEndDate])
            ->where('recoveries.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal6 = Recovery::with('order')
            ->whereBetween('pay_date', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $refunds = DB::table('refunds')
            ->join('methods', 'refunds.payment_type', '=', 'methods.code')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as ref_method',
                'methods.code as ref_code',
                'customers.name as ref_customer',
                'orders.invoice_no as ref_invoice',
                'refunds.payment_type as ref_pay_type',
                'refunds.refund_date as ref_date',
                DB::raw('SUM(refunds.pay) as ref_total')
            )
            ->whereBetween('refunds.refund_date', [$monthStartDate, $monthEndDate])
            ->where('refunds.is_deleted', 0)
            ->groupBy('refunds.order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal7 = Refund::with('order')
            ->whereBetween('refund_date', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $dues = DB::table('dues')
            ->join('orders', 'dues.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'orders.invoice_no as due_invoice',
                'customers.name as due_customer',
                'dues.due_date as due_date',
                'dues.comment as due_comment',
                DB::raw('SUM(dues.due) as due_total')
            )
            ->whereBetween('dues.due_date', [$monthStartDate, $monthEndDate])
            ->where('dues.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());
        /*
        $subTotal8 = Due::with('order')
            ->whereBetween('due_date',[$monthStartDate, $monthEndDate])
            -> sum('due');
         */
        $subTotal8 = Order::whereBetween('updated_at', [$monthStartDate, $monthEndDate])
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $total = $total_paid - $subTotal7 - $subTotal3 - $subTotal4 + $subTotal6;

        return view('products.cashflow', [
            'sales' => $sales,
            'title' => $title,
            'subTotal1' => $subTotal1,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'total_refund' => $total_refund,
            'purchases' => $purchases,
            'subTotal2' => $subTotal2,
            'expenses' => $expenses,
            'subTotal3' => $subTotal3,
            'deposits' => $deposits,
            'subTotal4' => $subTotal4,
            'damages' => $damages,
            'subTotal5' => $subTotal5,
            'recoveries' => $recoveries,
            'subTotal6' => $subTotal6,
            'refunds' => $refunds,
            'subTotal7' => $subTotal7,
            'dues' => $dues,
            'subTotal8' => $subTotal8,
            'total' => $total,
        ]);

    }


    /**
     * Display current week cash flow.
     */
    public function currentYearCashReport()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $now = Carbon::now();
        $yearStartDate = $now->startOfYear()->format('Y-m-d');
        $yearEndDate = $now->endOfYear()->format('Y-m-d');

        $sales = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                'order_details.created_at as o_date',
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->whereBetween('order_details.created_at', [$yearStartDate, $yearEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('p_name')
            ->paginate($row)
            ->appends(request()->query());

        $title = "Cashflow of " . Carbon::now()->format('Y');

        $subTotal1 = OrderDetails::with('product')
            ->whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $total_paid = Order::whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');
        $total_due = Order::whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');
        $total_refund = Order::whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('due', '<', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $purchases = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('purchase_details.is_deleted', 0)
            ->select(
                'products.product_name as pur_name',
                'purchase_details.product_id as pur_id',
                'purchase_details.created_at as pur_date',
                DB::raw('SUM(purchase_details.quantity) as pur_quantity'),
                DB::raw('SUM(purchase_details.total) as pur_total')
            )
            ->whereBetween('purchase_details.created_at', [$yearStartDate, $yearEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('pur_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal2 = PurchaseDetails::with('product')
            ->whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $expenses = DB::table('issues')
            ->join('departments', 'issues.department_id', '=', 'departments.id')
            ->join('units', 'issues.unit_id', '=', 'units.id')
            ->join('expense_details', 'issues.id', '=', 'expense_details.issue_id')
            ->where('departments.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('expense_details.is_deleted', 0)
            ->select(
                'issues.issue_name as exp_name',
                'expense_details.issue_id as exp_id',
                'expense_details.created_at as exp_date',
                DB::raw('SUM(expense_details.occurence) as exp_quantity'),
                DB::raw('SUM(expense_details.total) as exp_total')
            )
            ->whereBetween('expense_details.created_at', [$yearStartDate, $yearEndDate])
            ->where('issues.is_deleted', 0)
            ->groupBy('exp_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal3 = ExpenseDetails::with('issue')
            ->whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $deposits = DB::table('deposits')
            ->join('banks', 'deposits.bank_id', '=', 'banks.id')
            ->where('banks.is_deleted', 0)
            ->select(
                'deposits.deposit_code as dep_code',
                'banks.name as dep_name',
                DB::raw('SUM(deposits.amount) as dep_total')
            )
            ->whereBetween('deposits.created_at', [$yearStartDate, $yearEndDate])
            ->where('deposits.is_deleted', 0)
            ->groupBy('bank_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal4 = Deposit::with('bank')
            ->whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('amount');

        $damages = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('damage_details', 'products.id', '=', 'damage_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('damage_details.is_deleted', 0)
            ->select(
                'products.product_name as dam_name',
                'damage_details.product_id as dam_id',
                'damage_details.created_at as dam_date',
                DB::raw('SUM(damage_details.quantity) as dam_quantity'),
                DB::raw('SUM(damage_details.total) as dam_total')
            )
            ->whereBetween('damage_details.created_at', [$yearStartDate, $yearEndDate])
            ->where('products.is_deleted', 0)
            ->groupBy('dam_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal5 = DamageDetails::with('product')
            ->whereBetween('created_at', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('total');

        $recoveries = DB::table('recoveries')
            ->join('methods', 'recoveries.payment_type', '=', 'methods.code')
            ->join('orders', 'recoveries.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as rec_method',
                'methods.code as rec_code',
                'customers.name as rec_customer',
                'orders.invoice_no as rec_invoice',
                'recoveries.payment_type as rec_pay_type',
                'recoveries.pay_date as rec_date',
                DB::raw('SUM(recoveries.pay) as rec_total')
            )
            ->whereBetween('recoveries.pay_date', [$yearStartDate, $yearEndDate])
            ->where('recoveries.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal6 = Recovery::with('order')
            ->whereBetween('pay_date', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $refunds = DB::table('refunds')
            ->join('methods', 'refunds.payment_type', '=', 'methods.code')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as ref_method',
                'methods.code as ref_code',
                'customers.name as ref_customer',
                'orders.invoice_no as ref_invoice',
                'refunds.payment_type as ref_pay_type',
                'refunds.refund_date as ref_date',
                DB::raw('SUM(refunds.pay) as ref_total')
            )
            ->whereBetween('refunds.refund_date', [$yearStartDate, $yearEndDate])
            ->where('refunds.is_deleted', 0)
            ->groupBy('refunds.order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal7 = Refund::with('order')
            ->whereBetween('refund_date', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sum('pay');

        $dues = DB::table('dues')
            ->join('orders', 'dues.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'orders.invoice_no as due_invoice',
                'customers.name as due_customer',
                'dues.due_date as due_date',
                'dues.comment as due_comment',
                DB::raw('SUM(dues.due) as due_total')
            )
            ->whereBetween('dues.due_date', [$yearStartDate, $yearEndDate])
            ->where('dues.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());
        /*
        $subTotal8 = Due::with('order')
            ->whereBetween('due_date',[$yearStartDate, $yearEndDate])
            -> sum('due');
         */

        $subTotal8 = Order::whereBetween('updated_at', [$yearStartDate, $yearEndDate])
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $total = $total_paid - $subTotal7 - $subTotal3 - $subTotal4 + $subTotal6;

        return view('products.cashflow', [
            'sales' => $sales,
            'title' => $title,
            'subTotal1' => $subTotal1,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'total_refund' => $total_refund,
            'purchases' => $purchases,
            'subTotal2' => $subTotal2,
            'expenses' => $expenses,
            'subTotal3' => $subTotal3,
            'deposits' => $deposits,
            'subTotal4' => $subTotal4,
            'damages' => $damages,
            'subTotal5' => $subTotal5,
            'recoveries' => $recoveries,
            'subTotal6' => $subTotal6,
            'refunds' => $refunds,
            'subTotal7' => $subTotal7,
            'dues' => $dues,
            'subTotal8' => $subTotal8,
            'total' => $total,
        ]);

    }

    /**
     * Filter Cash Report
     */

    public function filterCashReport(Request $request)
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        if (Carbon::parse($fromDate) < Carbon::parse($toDate)) {
            $argument = [$fromDate, $toDate];
            $logic = "whereBetween";
            $title = "Cash-flow from " . Carbon::parse($fromDate)->format('M d, Y') . " to " .
                Carbon::parse($toDate)->format('M d, Y');
        } elseif (Carbon::parse($fromDate) == Carbon::parse($toDate)) {
            $argument = $fromDate;
            $logic = "whereDate";
            $title = "Cash-flow of " . Carbon::parse($fromDate)->format('M d, Y');
        } else {
            $argument = [$toDate, $fromDate];
            $logic = "whereBetween";
            $title = "Cash-flow from " . Carbon::parse($toDate)->format('M d, Y') . " to " .
                Carbon::parse($fromDate)->format('M d, Y');
        }

        $sales = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                'order_details.created_at as o_date',
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->$logic('order_details.created_at', $argument)
            ->where('products.is_deleted', 0)
            ->groupBy('p_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal1 = OrderDetails::with('product')
            ->$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('total');

        $total_paid = Order::$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('pay');
        $total_due = Order::$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->where('due', '>', 0)
            ->sum('due');
        $total_refund = Order::$logic('created_at', $argument)
            ->where('due', '<', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $purchases = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('purchase_details.is_deleted', 0)
            ->select(
                'products.product_name as pur_name',
                'purchase_details.product_id as pur_id',
                'purchase_details.created_at as pur_date',
                DB::raw('SUM(purchase_details.quantity) as pur_quantity'),
                DB::raw('SUM(purchase_details.total) as pur_total')
            )
            ->$logic('purchase_details.created_at', $argument)
            ->where('products.is_deleted', 0)
            ->groupBy('pur_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal2 = PurchaseDetails::with('product')
            ->$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('total');

        $expenses = DB::table('issues')
            ->join('departments', 'issues.department_id', '=', 'departments.id')
            ->join('units', 'issues.unit_id', '=', 'units.id')
            ->join('expense_details', 'issues.id', '=', 'expense_details.issue_id')
            ->where('departments.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('expense_details.is_deleted', 0)
            ->select(
                'issues.issue_name as exp_name',
                'expense_details.issue_id as exp_id',
                'expense_details.created_at as exp_date',
                DB::raw('SUM(expense_details.occurence) as exp_quantity'),
                DB::raw('SUM(expense_details.total) as exp_total')
            )
            ->$logic('expense_details.created_at', $argument)
            ->where('issues.is_deleted', 0)
            ->groupBy('exp_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal3 = ExpenseDetails::with('issue')
            ->$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('total');

        $deposits = DB::table('deposits')
            ->join('banks', 'deposits.bank_id', '=', 'banks.id')
            ->where('banks.is_deleted', 0)
            ->select(
                'deposits.deposit_code as dep_code',
                'banks.name as dep_name',
                DB::raw('SUM(deposits.amount) as dep_total')
            )
            ->$logic('deposits.created_at', $argument)
            ->where('deposits.is_deleted', 0)
            ->groupBy('deposits.bank_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal4 = Deposit::with('bank')
            ->$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('amount');

        $damages = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('damage_details', 'products.id', '=', 'damage_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('damage_details.is_deleted', 0)
            ->select(
                'products.product_name as dam_name',
                'damage_details.product_id as dam_id',
                'damage_details.created_at as dam_date',
                DB::raw('SUM(damage_details.quantity) as dam_quantity'),
                DB::raw('SUM(damage_details.total) as dam_total')
            )
            ->$logic('damage_details.created_at', $argument)
            ->where('products.is_deleted', 0)
            ->groupBy('dam_name')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal5 = DamageDetails::with('product')
            ->$logic('created_at', $argument)
            ->where('is_deleted', 0)
            ->sum('total');

        $recoveries = DB::table('recoveries')
            ->join('methods', 'recoveries.payment_type', '=', 'methods.code')
            ->join('orders', 'recoveries.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as rec_method',
                'methods.code as rec_code',
                'customers.name as rec_customer',
                'orders.invoice_no as rec_invoice',
                'recoveries.payment_type as rec_pay_type',
                'recoveries.pay_date as rec_date',
                DB::raw('SUM(recoveries.pay) as rec_total')
            )
            ->$logic('recoveries.pay_date', $argument)
            ->where('recoveries.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal6 = Recovery::with('order')
            ->$logic('pay_date', $argument)
            ->where('is_deleted', 0)
            ->sum('pay');

        $refunds = DB::table('refunds')
            ->join('methods', 'refunds.payment_type', '=', 'methods.code')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as ref_method',
                'methods.code as ref_code',
                'customers.name as ref_customer',
                'orders.invoice_no as ref_invoice',
                'refunds.payment_type as ref_pay_type',
                'refunds.refund_date as ref_date',
                DB::raw('SUM(refunds.pay) as ref_total')
            )
            ->$logic('refunds.refund_date', $argument)
            ->where('refunds.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());

        $subTotal7 = Refund::with('order')
            ->$logic('refund_date', $argument)
            ->where('is_deleted', 0)
            ->sum('pay');

        $dues = DB::table('dues')
            ->join('orders', 'dues.order_id', '=', 'orders.id')
            ->where('orders.is_deleted', 0)
            ->select(
                'orders.invoice_no as due_invoice',
                'dues.customer as due_customer',
                'dues.due_date as due_date',
                'dues.comment as due_comment',
                DB::raw('SUM(orders.due) as due_total')
            )
            ->$logic('dues.due_date', $argument)
            ->where('orders.due', '>', 0)
            ->where('dues.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());


        $subTotal8 = Order::$logic('updated_at', $argument)
            ->where('due', '>', 0)
            ->where('is_deleted', 0)
            ->sum('due');

        $total = $total_paid - $subTotal7 - $subTotal3 - $subTotal4 + $subTotal6;

        return view('products.cashflow', [
            'sales' => $sales,
            'title' => $title,
            'subTotal1' => $subTotal1,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'total_refund' => $total_refund,
            'purchases' => $purchases,
            'subTotal2' => $subTotal2,
            'expenses' => $expenses,
            'subTotal3' => $subTotal3,
            'deposits' => $deposits,
            'subTotal4' => $subTotal4,
            'damages' => $damages,
            'subTotal5' => $subTotal5,
            'recoveries' => $recoveries,
            'subTotal6' => $subTotal6,
            'refunds' => $refunds,
            'subTotal7' => $subTotal7,
            'dues' => $dues,
            'subTotal8' => $subTotal8,
            'total' => $total,
        ]);

    }

    /**
     * Show the form input date for order report.
     */
    public function getOrderReport()
    {
        return view('orders.report-order');
    }

    /**
     * Handle request to get order report
     */
    public function exportOrderReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        // $orderDetails = DB::table('orders')
        // ->whereBetween('orders.order_date',[$sDate,$eDate])
        // ->where('orders.order_status','1')
        // ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        // ->get();

        $orders = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('products.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->whereBetween('orders.order_date', [$sDate, $eDate])
            ->where('orders.order_status', '1')
            ->select(
                'orders.invoice_no',
                'orders.order_date',
                'orders.customer_id',
                'products.product_code',
                'products.product_name',
                'order_details.quantity',
                'order_details.unitcost',
                'order_details.total'
            )
            ->where('order_details.is_deleted', 0)
            ->get();


        $order_array [] = array(
            'Date',
            'No Invoice',
            'Customer',
            'Product Code',
            'Product',
            'Quantity',
            'Unitcost',
            'Total',
        );

        foreach ($orders as $order) {
            $order_array[] = array(
                'Date' => $order->order_date,
                'No Invoice' => $order->invoice_no,
                'Customer' => $order->customer_id,
                'Product Code' => $order->product_code,
                'Product' => $order->product_name,
                'Quantity' => $order->quantity,
                'Unitcost' => $order->unitcost,
                'Total' => $order->total,
            );
        }

        $this->exportExcel($order_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="order-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Display an all orders.
     */
    public function allOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('customers.is_deleted', 0)
            ->select(
                'customers.name as customer',
                'orders.id as id',
                'orders.invoice_no as invoice_no',
                'orders.order_date as order_date',
                'orders.order_status as order_status',
                'orders.is_confirmed as is_confirmed',
                'orders.updated_at as updated_at',
                'orders.created_at as created_at',
                'orders.payment_type as payment_type',
                'orders.due as due',
                'orders.pay as pay',
                'orders.total as total',
                'orders.vat as vat'
            )
            ->where('orders.is_deleted', 0)
            ->orderByDesc('order_date')
            ->orderByDesc('created_at')
            ->paginate($row)
            ->appends(request()
                ->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }


    public function filter(Request $request)
    {

        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $orders = Order::with(['customer'])
            ->whereBetween('order_date', [$fromDate, $toDate])
            ->where('is_deleted', 0)
            ->sortable()
            ->paginate($row)
            ->appends(request()
                ->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display an currentWeek orders.
     */
    public function currentWeekOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        $orders = Order::with(['customer'])
            ->whereBetween('order_date', [$weekStartDate, $weekEndDate])
            ->where('is_deleted', 0)
            ->sortable()
            ->paginate($row)
            ->appends(request()
                ->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display an currentMonth orders.
     */
    public function currentMonthOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $now = Carbon::now();
        $monthStartDate = $now->startOfMonth()->format('Y-m-d');
        $monthEndDate = $now->endOfMonth()->format('Y-m-d');

        $orders = Order::with(['customer'])
            ->whereBetween('order_date', [$monthStartDate, $monthEndDate])
            ->where('is_deleted', 0)
            ->sortable()
            ->paginate($row)
            ->appends(request()
                ->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }

    /**
     * Display an currentYear orders.
     */
    public function currentYearOrders()
    {
        $row = (int)request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $now = Carbon::now();
        $yearStartDate = $now->startOfYear()->format('Y-m-d');
        $yearEndDate = $now->endOfYear()->format('Y-m-d');

        $orders = Order::with(['customer'])
            ->whereBetween('order_date', [$yearStartDate, $yearEndDate])
            ->where('is_deleted', 0)
            ->sortable()
            ->paginate($row)
            ->appends(request()
                ->query());

        $cashInHand = $orders->where('payment_type', '=', 'HandCash')->sum('pay');
        $cashMoMo = $orders->where('payment_type', '=', 'MoMo')->sum('pay');
        $cashInCheque = $orders->where('payment_type', '=', 'Cheque')->sum('pay');
        $cashDue = $orders->where('due', '>', 0)->sum('due');
        $cashOver = $orders->where('due', '<', 0)->sum('due');

        $totalInvoices = $orders->sum('total');
        $totalPaid = $orders->sum('pay');
        $totalTax = $orders->sum('vat');
        $balance = $totalInvoices - $totalPaid;

        return view('orders.orders', [
            'orders' => $orders,
            'totalInvoices' => $totalInvoices,
            'totalPaid' => $totalPaid,
            'totalTax' => $totalTax,
            'balance' => $balance,
            'cashInHand' => $cashInHand,
            'cashMoMo' => $cashMoMo,
            'cashInCheque' => $cashInCheque,
            'cashDue' => $cashDue,
            'cashOver' => $cashOver,
        ]);
    }


    /**
     * Handle delete an order
     */
    public function deleteOrder($id)
    {
        $order = Order::getSingle($id);
        $order->is_deleted = 1;
        $order->save();

        // Order::destroy($order->id);

        OrderDetails::where('order_id', $order->id)->update([
            'is_deleted' => 1
        ]);

        Due::where('order_id', $order->id)->update([
            'is_deleted' => 1
        ]);

        Refund::where('order_id', $order->id)->update([
            'is_deleted' => 1
        ]);

        Recovery::where('order_id', $order->id)->update([
            'is_deleted' => 1
        ]);

        return Redirect::route('orders.allOrders')->with('success', 'Order has been deleted!');
    }

    /**
     * Handle restore an order
     */
    public function restoreOrder($id)
    {
        $order = Order::getSingle($id);
        $order->is_deleted = 0;
        $order->save();

        // Order::destroy($order->id);

        OrderDetails::where('order_id', $order->id)->update([
            'is_deleted' => 0
        ]);

        Due::where('order_id', $order->id)->update([
            'is_deleted' => 0
        ]);

        Refund::where('order_id', $order->id)->update([
            'is_deleted' => 0
        ]);

        Recovery::where('order_id', $order->id)->update([
            'is_deleted' => 0
        ]);

        return Redirect::route('orders.allOrders')->with('success', 'Order has been deleted!');
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getRefunds(int $row): LengthAwarePaginator
    {
        return DB::table('refunds')
            ->join('methods', 'refunds.payment_type', '=', 'methods.code')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as ref_method',
                'methods.code as ref_code',
                'customers.name as ref_customer',
                'orders.invoice_no as ref_invoice',
                'refunds.payment_type as ref_pay_type',
                'refunds.refund_date as ref_date',
                DB::raw('SUM(refunds.pay) as ref_total')
            )
            ->whereDate('refunds.refund_date', '=', Carbon::now()->format('Y-m-d'))
            ->where('refunds.is_deleted', 0)
            ->groupBy('refunds.order_id')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getDues(int $row): LengthAwarePaginator
    {
        return DB::table('dues')
            ->join('orders', 'dues.order_id', '=', 'orders.id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'orders.invoice_no as due_invoice',
                'customers.name as due_customer',
                'dues.due_date as due_date',
                'dues.comment as due_comment',
                DB::raw('SUM(dues.due) as due_total')
            )
            ->whereDate('dues.due_date', '=', Carbon::now()->format('Y-m-d'))
            ->where('dues.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getRecoveries(int $row): LengthAwarePaginator
    {
        return DB::table('recoveries')
            ->join('methods', 'recoveries.payment_type', '=', 'methods.code')
            ->join('orders', 'recoveries.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('methods.is_deleted', 0)
            ->where('orders.is_deleted', 0)
            ->where('customers.is_deleted', 0)
            ->select(
                'methods.name as rec_method',
                'methods.code as rec_code',
                'customers.name as rec_customer',
                'orders.invoice_no as rec_invoice',
                'recoveries.payment_type as rec_pay_type',
                'recoveries.pay_date as rec_date',
                DB::raw('SUM(recoveries.pay) as rec_total')
            )
            ->whereDate('recoveries.pay_date', '=', Carbon::now()->format('Y-m-d'))
            ->where('recoveries.is_deleted', 0)
            ->groupBy('order_id')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getDamages(int $row): LengthAwarePaginator
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('damage_details', 'products.id', '=', 'damage_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('damage_details.is_deleted', 0)
            ->select(
                'products.product_name as dam_name',
                'damage_details.product_id as dam_id',
                'damage_details.created_at as dam_date',
                DB::raw('SUM(damage_details.quantity) as dam_quantity'),
                DB::raw('SUM(damage_details.total) as dam_total')
            )
            ->whereDate('damage_details.created_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('products.is_deleted', 0)
            ->groupBy('dam_name')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getDeposits(int $row): LengthAwarePaginator
    {
        return DB::table('deposits')
            ->join('banks', 'deposits.bank_id', '=', 'banks.id')
            ->where('banks.is_deleted', 0)
            ->select(
                'deposits.deposit_code as dep_code',
                'banks.name as dep_name',
                DB::raw('SUM(deposits.amount) as dep_total')
            )
            ->whereDate('deposits.created_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('deposits.is_deleted', 0)
            ->groupBy('deposits.bank_id')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getExpenses(int $row): LengthAwarePaginator
    {
        return DB::table('issues')
            ->join('departments', 'issues.department_id', '=', 'departments.id')
            ->join('units', 'issues.unit_id', '=', 'units.id')
            ->join('expense_details', 'issues.id', '=', 'expense_details.issue_id')
            ->where('departments.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('expense_details.is_deleted', 0)
            ->select(
                'issues.issue_name as exp_name',
                'expense_details.issue_id as exp_id',
                'expense_details.created_at as exp_date',
                DB::raw('SUM(expense_details.occurence) as exp_quantity'),
                DB::raw('SUM(expense_details.total) as exp_total')
            )
            ->whereDate('expense_details.created_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('issues.is_deleted', 0)
            ->groupBy('exp_name')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getPurchases(int $row): LengthAwarePaginator
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('purchase_details.is_deleted', 0)
            ->select(
                'products.product_name as pur_name',
                'purchase_details.product_id as pur_id',
                'purchase_details.created_at as pur_date',
                DB::raw('SUM(purchase_details.quantity) as pur_quantity'),
                DB::raw('SUM(purchase_details.total) as pur_total')
            )
            ->whereDate('purchase_details.created_at', '=', Carbon::now()->format('Y-m-d'))
            ->where('products.is_deleted', 0)
            ->groupBy('pur_name')
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param int $row
     * @return LengthAwarePaginator
     */
    public function getSales(int $row): LengthAwarePaginator
    {
        $from_date = request('from_date', Carbon::now()->startOfDay()->format('Y-m-d'));
        $to_date = request('to_date', Carbon::now()->endOfDay()->format('Y-m-d'));

        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('categories.is_deleted', 0)
            ->where('units.is_deleted', 0)
            ->where('order_details.is_deleted', 0)
            ->where('products.is_deleted', 0)
            ->select(
                'products.product_name as p_name',
                'products.product_image as p_image',
                'categories.name as c_name',
                'units.name as u_name',
                'order_details.product_id as p_id',
                DB::raw('DATE(orders.updated_at) as o_date'), // Use the order's created date
                DB::raw('SUM(order_details.quantity) as p_quantity'),
                DB::raw('SUM(order_details.total) as p_sales')
            )
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('orders.updated_at', '>=', $from_date); // Filter by order's created date
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('orders.updated_at', '<=', $to_date); // Filter by order's created date
            })
            ->groupBy('products.id') // Ensure grouping by unique product ID
            ->paginate($row)
            ->appends(request()->query());
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalPaid(mixed $from_date, mixed $to_date): mixed
    {
        return Order::query()
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('updated_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('updated_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('pay');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalDue(mixed $from_date, mixed $to_date): mixed
    {
        return Order::query()
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('updated_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('updated_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('due');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalRefund(mixed $from_date, mixed $to_date): mixed
    {
        return Order::query()
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('updated_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('updated_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->where('due', '<', 0)
            ->sum('due');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getSalesValue(mixed $from_date, mixed $to_date): mixed
    {
        return OrderDetails::with('product')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('total');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getPurchasesValue(mixed $from_date, mixed $to_date): mixed
    {
        return PurchaseDetails::with('product')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('total');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalExpenses(mixed $from_date, mixed $to_date): mixed
    {
        return ExpenseDetails::with('issue')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('total');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalDeposits(mixed $from_date, mixed $to_date): mixed
    {
        return Deposit::with('bank')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('amount');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalDamages(mixed $from_date, mixed $to_date): mixed
    {
        return DamageDetails::with('product')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('total');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalRecoveries(mixed $from_date, mixed $to_date): mixed
    {
        return Recovery::with('order')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('pay');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalRefunds(mixed $from_date, mixed $to_date): mixed
    {
        return Refund::with('order')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('is_deleted', 0)
            ->sum('pay');
    }

    /**
     * @param mixed $from_date
     * @param mixed $to_date
     * @return int|mixed
     */
    public function getTotalDues(mixed $from_date, mixed $to_date): mixed
    {
        return Order::query()
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('created_at', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('created_at', '<=', $to_date);
            })
            ->where('due', '>', 0)
            ->sum('due');
    }

}
