<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Damage;
use Illuminate\Http\Request;
use App\Models\DamageDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DamageController extends Controller
{
    /**
     * Display an all damages.
     */
    public function allDamages()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $damages = Damage::sortable()
            ->where('is_deleted',0)
            ->orderByDesc('updated_at','created_at')
            ->paginate($row)
            ->appends(request()->query());

        return view('damages.damages', [
            'damages' => $damages
        ]);
    }

    /**
     * Display an all approved damages.
     */
    public function approvedDamages()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $damages = Damage::where('damage_status', 1) // 1 = approved
            ->where('is_deleted',0)
            ->orderByDesc('updated_at','created_at')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('damages.approved-damages', [
            'damages' => $damages
        ]);
    }

    /**
     * Display a damage details.
     */
    public function damageDetails(String $damage_id)
    {
        $damage = Damage::with(['user_created','user_updated'])
            ->where('is_deleted',0)
            ->where('id', $damage_id)
            ->first();

        $damageDetails = DamageDetails::with('product')
            ->where('damage_id', $damage_id)
            ->where('is_deleted',0)
            ->orderBy('id')
            ->get();

        return view('damages.details-damage', [
            'damage' => $damage,
            'damageDetails' => $damageDetails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDamage()
    {
        return view('damages.create-damage', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeDamage(Request $request)
    {
        $rules = [
            'damage_date' => 'required|string',
            'total_amount' => 'required|numeric'
        ];

        $damage_no = IdGenerator::generate([
            'table' => 'damages',
            'field' => 'damage_no',
            'length' => 8,
            'prefix' => 'DAM-'
        ]);

        $validatedData = $request->validate($rules);

        $validatedData['damage_status'] = 0; // 0 = pending, 1 = approved
        $validatedData['damage_no'] = $damage_no;
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['created_at'] = Carbon::now();

        $damage_id = Damage::insertGetId($validatedData);

        // Create Damage Details
        $pDetails = array();
        $products = count($request->product_id);
        for ($i=0; $i < $products; $i++) {
            $pDetails['damage_id'] = $damage_id;
            $pDetails['product_id'] = $request->product_id[$i];
            $pDetails['quantity'] = $request->quantity[$i];
            $pDetails['unitcost'] = $request->unitcost[$i];
            $pDetails['total'] = $request->total[$i];
            $pDetails['created_at'] = Carbon::now();

            DamageDetails::insert($pDetails);
        }

        return Redirect::route('damages.allDamages')->with('success', 'Damage has been created!');
    }

    /**
     * Handle update a status damage
     */
    public function updateDamage(Request $request)
    {
        $damage_id = $request->id;

        // after damage approved, add stock product
        $products = DamageDetails::where('damage_id', $damage_id)->get();

        foreach ($products as $product) {
            $isOpened = Stock::where('product_id', $product -> product_id)
                            ->whereDate('stock_date', Carbon::now())
                            ->first();

            if(empty($isOpened) || $isOpened == null) {
                $opening = Product::where('id', $product -> product_id)
                    ->where('is_deleted',0)
                    ->first();

                Stock::insert([
                    'product_id' => $product->product_id,
                    'opening' => $opening -> stock,
                    'buying_price' => $opening -> buying_price,
                    'stock_value' => $opening -> stock * $opening -> selling_price,
                    'sales' => 0,
                    'sale_value' => 0,
                    'purchases' => 0,
                    'purchase_value' => 0,
                    'damages' => $product -> quantity,
                    'damage_value' => $product -> total,
                    'stock_date' => Carbon::now()->format('Y-m-d'),
                    'closing' => $opening -> stock - $product->quantity,
                    'closing_value' => ($opening -> stock - $product->quantity) * $opening -> selling_price,
                    'created_at' => Carbon::now()
                ]);
            } else {
                $currentStock = Stock::where('product_id', $product -> product_id)
                    ->whereDate('stock_date', Carbon::now())
                    ->first();

                $c_product = Product::where('id', $product -> product_id)->first();

                Stock::where('product_id', $product -> product_id)
                    ->where('is_deleted',0)
                    ->whereDate('stock_date', Carbon::now())
                    ->update([
                        'damages' => $currentStock -> damages + $product -> quantity,
                        'damage_value' => $currentStock -> damage_value + $product -> total,
                        'closing' => $currentStock -> closing - $product -> quantity,
                        'closing_value' => ($currentStock -> closing - $product -> quantity) * $c_product -> buying_price,
                    ]);
            }

            Product::where('id', $product->product_id)
                    ->where('is_deleted',0)
                    ->update([
                        'stock' => DB::raw('stock-'.$product->quantity),
                        'buying_price' => $product->unitcost
                    ]);
        }

        Damage::findOrFail($damage_id)
            ->update([
                'damage_status' => 1,
                'updated_by' => auth()->user()->id
            ]); // 1 = approved, 0 = pending

        return Redirect::route('damages.allDamages')->with('success', 'Damage has been approved!');
    }

    /**
     * Handle delete a damage
     */
    public function deleteDamage(String $damage_id)
    {
        Damage::where([
            'id' => $damage_id,
            'damage_status' => '0'
        ])->delete();

        DamageDetails::where('damage_id', $damage_id)->delete();

        return Redirect::route('damages.allDamages')->with('success', 'Damage has been deleted!');
    }

    /**
     * Display an all damages.
     */
    public function dailyDamageReport()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $damages = Damage::where('damage_date', Carbon::now()->format('Y-m-d')) // 1 = approved
            ->where('is_deleted',0)
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('damages.damages', [
            'damages' => $damages
        ]);
    }

    /**
     * Show the form input date for damage report.
     */
    public function getDamageReport()
    {
        return view('damages.report-damage');
    }

    /**
     * Handle request to get damage report
     */
    public function exportDamageReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        // $damageDetails = DB::table('damages')
        //     ->whereBetween('damages.damage_date',[$sDate,$eDate])
        //     ->where('damages.damage_status','1')
        //     ->join('damage_details', 'damages.id', '=', 'damage_details.damage_id')
        //     ->get();

        $damages = DB::table('damage_details')
            ->join('products', 'damage_details.product_id', '=', 'products.id')
            ->join('damages', 'damage_details.damage_id', '=', 'damages.id')
            ->whereBetween('damages.damage_date',[$sDate,$eDate])
            ->where('damages.damage_status','1')
            ->where('damages.is_deleted',0)
            ->select( 'damages.damage_no', 'damages.damage_date', 'damages.supplier_id','products.product_code', 'products.product_name', 'damage_details.quantity', 'damage_details.unitcost', 'damage_details.total')
            ->get();


        $damage_array [] = array(
            'Date',
            'No Damage',
            'Product Code',
            'Product',
            'Quantity',
            'Unitcost',
            'Total',
        );

        foreach($damages as $damage)
        {
            $damage_array[] = array(
                'Date' => $damage->damage_date,
                'No Damage' => $damage->damage_no,
                'Product Code' => $damage->product_code,
                'Product' => $damage->product_name,
                'Quantity' => $damage->quantity,
                'Unitcost' => $damage->unitcost,
                'Total' => $damage->total,
            );
        }

        $this->exportExcel($damage_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($products){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="damage-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
