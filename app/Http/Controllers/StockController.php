<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Damage;
use App\Models\DamageDetails;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $stocks = Stock::with('product')
                ->filter(request(['search']))
                ->orderByDesc('stock_date')
                ->orderByDesc('updated_at')
                ->orderByDesc('created_at')
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        return view('stocks.index', [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stocks.create', [
            'products' => Product::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules = [
            'product_id' => 'required|integer',
            'opening' => 'required|integer',
            'sales' => 'required|integer',
            'purchases' => 'required|integer',
            'damages' => 'required|integer',
            'closing' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);


        Stock::create($validatedData);

        return Redirect::route('stocks.index')->with('success', 'Stock has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {

        return view('stocks.show', [
            'stock' => $stock,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        return view('stocks.edit', [
            'products' => Product::all(),
            'stock' => $stock
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $rules = [
            'product_id' => 'required|integer',
            'opening' => 'required|integer',
            'sales' => 'required|integer',
            'purchases' => 'required|integer',
            'damages' => 'required|integer',
            'closing' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        Stock::where('id', $stock->id)->update($validatedData);

        return Redirect::route('stocks.index')->with('success', 'Stock has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {

        Stock::destroy($stock->id);

        return Redirect::route('stocks.index')->with('success', 'Stock has been deleted!');
    }
    
    /**
     * Delete stock 
     */

    public function deleteStock($id)
    {
        $stock = Stock::getSingle($id);
        $stock -> is_deleted = 1;
        $stock -> save();

        if(Str::contains($stock->reference, 'INV-'))
        {
            $order = Order::where('invoice_no',$stock->reference)
                        ->first();
            
            $products = OrderDetails::where('order_id', $order->id)
                                    ->get();
            foreach($products as $product)
            {
                $sales = $product->quantity;
                $product -> is_deleted = 1;
                $product -> save();

                $old_stock = Product::where('id',$product->product_id)
                                    ->first();
                $new_stock = $old_stock->stock + $sales;

                Product::where('id',$product->product_id)
                        ->update([
                            'stock' => $new_stock
                        ]);
            }            
        }

        if(Str::contains($stock->reference, 'PRS-'))
        {
            $purchase = Purchase::where('purchase_no',$stock->reference)
                        ->first();
            $products = PurchaseDetails::where('purchase_id', $purchase->id)
                                    ->get();
            foreach($products as $product)
            {
                $purchases = $product->quantity;

                $product -> is_deleted = 1;
                $product -> save();

                $old_stock = Product::where('id',$product->product_id)
                                    ->first();
                $new_stock = $old_stock->stock - $purchases;

                Product::where('id',$product->product_id)
                        ->update([
                            'stock' => $new_stock
                        ]);
            }
        }

        if(Str::contains($stock->reference, 'DAM'))
        {
            $damage = Damage::where('damage_no',$stock->reference)
                        ->first();
            $products = DamageDetails::where('damage_id', $damage->id)
                                    ->get();
            foreach($products as $product)
            {
                $damages = $product->quantity;

                $product -> is_deleted = 1;
                $product -> save();

                $old_stock = Product::where('id',$product->product_id)
                                    ->first();
                $new_stock = $old_stock->stock + $damages;

                Product::where('id',$product->product_id)
                        ->update([
                            'stock' => $new_stock
                        ]);
            }
        }

    }

    /**
     * Handle export data stocks.
     */
    public function import()
    {
        return view('stocks.import');
    }

    public function handleImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $the_file = $request->file('file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'J', $column_limit );
            $startcount = 2;
            $data = array();
            foreach ( $row_range as $row ) {
                $data[] = [
                    'stock_name' => $sheet->getCell( 'A' . $row )->getValue(),
                    'product_id' => $sheet->getCell( 'B' . $row )->getValue(),
                    'unit_id' => $sheet->getCell( 'C' . $row )->getValue(),
                    'stock_code' => $sheet->getCell( 'D' . $row )->getValue(),
                    'stock' => $sheet->getCell( 'E' . $row )->getValue(),
                    'buying_price' => $sheet->getCell( 'F' . $row )->getValue(),
                    'selling_price' =>$sheet->getCell( 'G' . $row )->getValue(),
                    'stock_image' =>$sheet->getCell( 'H' . $row )->getValue(),
                ];
                $startcount++;
            }

            Stock::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('stocks.index')->with('error', 'There was a problem uploading the data!');
        }
        return Redirect::route('stocks.index')->with('success', 'Data stock has been imported!');
    }

    /**
     * Handle export data stocks.
     */
    function export()
    {
        $stocks = Stock::all()->sortBy('stock_name');

        $stock_array [] = array(
            'Product Name',
            'Category Id',
            'Unit Id',
            'Stock Code',
            'Stock',
            'Buying Price',
            'Selling Price',
            'Stock Image',
        );

        foreach($stocks as $stock)
        {
            $stock_array[] = array(
                'Stock Name' => $stock->stock_name,
                'Category Id' => $stock->product_id,
                'Unit Id' => $stock->unit_id,
                'Stock Code' => $stock->stock_code,
                'Stock' => $stock->stock,
                'Buying Price' =>$stock->buying_price,
                'Selling Price' =>$stock->selling_price,
                'Stock Image' => $stock->stock_image,
            );
        }

        $this->exportExcel($stock_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($stocks)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($stocks);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="stocks.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    /**
    * Display today cash flow.
    */
    public function dailyStockReport()
    {
        $row = (int) request('row', 100);

        if ($row < 1 || $row> 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
            }

        $stocks = DB::table('products')
            -> join('damage_details', 'products.id', '=', 'damage_details.product_id')
            -> join('purchase_details', 'products.id', '=', 'purchase_details.product_id')
            -> join('order_details', 'products.id', '=', 'order_details.product_id')
            -> select(
                'products.product_name as p_name',
                'products.stock as opening',
                DB::raw('SUM(order_details.quantity) as sales'),
                DB::raw('SUM(purchase_details.quantity) as purchases'),
                DB::raw('SUM(damage_details.quantity) as damages'),
                'stock - sales + purchases - damages as closing'
            )
            -> whereDate('order_details.created_at', '=', Carbon::now() -> format('Y-m-d'))
            -> whereDate('purchase_details.created_at', '=', Carbon::now() -> format('Y-m-d'))
            -> whereDate('damage_details.created_at', '=', Carbon::now() -> format('Y-m-d'))
            -> orderByDesc('order_details.created_at')
            -> groupBy('p_name')
            -> paginate($row)
            -> appends(request()->query());

        $title = "Stock report of ". Carbon::now() -> format('M d, Y');
        return view('stocks.index', [
            'title' => $title,
            'stocks' => $stocks,
        ]);

    }
    public function filter(Request $request)
    {

        $row = (int) request('row', 100);

        if ($row < 1 || $row> 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $fromDate = $request -> from_date;
        $toDate = $request -> to_date;
        if (Carbon::parse($fromDate) < Carbon::parse($toDate))
        {
            $argument = [$fromDate, $toDate];
            $logic = "whereBetween";
            $title = "Active products from " . Carbon::parse($fromDate)->format('M d, Y') . " to " .
            Carbon::parse($toDate) ->format('M d, Y');
        } elseif (Carbon::parse($fromDate) == Carbon::parse($toDate))
        {
            $argument = $fromDate;
            $logic = "whereDate";
            $title = "Active products of " . Carbon::parse($fromDate)->format('M d, Y');
        } else
        {
            $argument = [$toDate, $fromDate];
            $logic = "whereBetween";
            $title = "Active products from " . Carbon::parse($toDate)->format('M d, Y') . " to " .
            Carbon::parse($fromDate) ->format('M d, Y');
        }

        $stocks = Stock::with(['product'])
                ->$logic('stock_date',$argument)
                ->orderByDesc('stock_date')
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        $totalOpening = $stocks ->sum('stock_value');
        $totalSales = $stocks -> sum('sale_value');
        $totalPurchases = $stocks -> sum('purchase_value');
        $totalDamages = $stocks -> sum('damage_value');
        $totalClosing = $stocks -> sum('closing_value');
        $cost = $totalOpening - $totalClosing;
        $profit = $totalSales - $cost;

        return view('stocks.stocks', [
            'stocks' => $stocks,
            'title' => $title,
            'totalOpening' => $totalOpening,
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalDamages' => $totalDamages,
            'totalClosing' => $totalClosing,
            'cost' => $cost,
            'profit' => $profit,
        ]);
    }


}
