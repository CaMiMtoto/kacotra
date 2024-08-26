<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Method;
use App\Models\Deposit;
use App\Models\Bank;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class DepositController extends Controller
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

        $deposits = Deposit::with(['method', 'bank'])
                ->where('is_deleted',0)
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        return view('deposits.index', [
            'deposits' => $deposits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return view('deposits.create', [
            'methods' => Method::where('id','<',5) ->where('is_deleted',0)->get(),
            'banks' => Bank::where('is_deleted',0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $deposit_code = IdGenerator::generate([
            'table' => 'deposits',
            'field' => 'deposit_code',
            'length' => 8,
            'prefix' => 'DEP'
        ]);

        $rules = [
            'bank_id' => 'required|integer',
            'amount' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        // Save deposit code value
        $validatedData['deposit_code'] = $deposit_code;
        $validatedData['deposit_date'] = Carbon::now() -> format('Y-m-d');
        $validatedData['deposit_status'] = 0;
        $validatedData['created_by'] = auth() -> user() -> id;
        $validatedData['updated_by'] = auth() -> user() -> id;
        $validatedData['created_at'] = Carbon::now();

        $deposit_id = Deposit::insertGetId($validatedData);

        /**
         * Record refunc to journal
         */
        $deposit = Deposit::where('id',$deposit_id)->first();

        $credit = $deposit -> amount;
        $user = $deposit -> created_by;
        $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
                            ->where('is_deleted',0)
                            ->first();

        $journals = Journal::where('is_deleted',0)->get();

        $comment = "Deposit";

        if (empty($journal) || $journal == null)
        {
            if ($journals->count()==0) {
                $opening_value = 0;
                $balance = -$credit;
            } else {
                $opening_value = $journals[$journals->count()-1] -> balance;
                $balance = $opening_value - $credit;
            }
        } else {
            $opening_value = $journal -> opening;
            $balance = $journals[$journals->count()-1] -> balance - $credit;
        }
        $description = "Cash Deposit @ " . $deposit->bank->name;
        $reference = $deposit->deposit_code;
        Journal::insert([
            'user_id' => $user,
            'journal_date' => Carbon::now()->format('Y-m-d'),
            'description' => $description,
            'reference' => $reference,
            'opening' => $opening_value,
            'debit' => 0,
            'credit' => $credit,
            'balance' => $balance,
            'comment' => $comment,
            'created_at' => Carbon::now()->format('Y-m-d'),
        ]);

        return Redirect::route('deposits.index')->with('success', 'Deposit has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit)
    {

        return view('deposits.show', [
            'deposit' => $deposit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit $deposit)
    {
        return view('deposits.edit', [
            'methods' => Method::where('is_deleted',0)->get(),
            'banks' => Bank::where('is_deleted',0)->get(),
            'deposit' => $deposit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        $rules = [
            'bank_id' => 'required|integer',
            'amount' => 'required|integer',
            'deposit_status' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        Deposit::where('id', $deposit->id)->update($validatedData);

        return Redirect::route('deposits.index')->with('success', 'Deposit has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        $deposit->is_deleted = 1;
        $deposit->save();
        // Deposit::destroy($deposit->id);

        return Redirect::route('deposits.index')->with('success', 'Deposit has been deleted!');
    }

    /**
     * Handle export data deposits.
     */
    public function import()
    {
        return view('deposits.import');
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
            $column_range = range( 'G', $column_limit );
            $startcount = 2;
            $data = array();
            foreach ( $row_range as $row ) {
                $data[] = [
                    'deposit_date' => $sheet->getCell( 'A' . $row )->getValue(),
                    'deposit_code' => $sheet->getCell( 'B' . $row )->getValue(),
                    'amount' => $sheet->getCell( 'C' . $row )->getValue(),
                    'deposit_status' => $sheet->getCell( 'D' . $row )->getValue(),
                    'created_by' =>$sheet->getCell( 'E' . $row )->getValue(),
                    'updated_by' =>$sheet->getCell( 'F' . $row )->getValue(),
                ];
                $startcount++;
            }

            Deposit::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('deposits.index')->with('error', 'There was a problem uploading the data!');
        }
        return Redirect::route('deposits.index')->with('success', 'Data deposit has been imported!');
    }

    /**
     * Handle export data deposits.
     */
    function export(){
        $deposits = Deposit::where('is_deleted',0)->sortBy('deposit_date')->get();

        $deposit_array [] = array(
            'Deposit Date',
            'Bank Id',
            'Deposit Code',
            'Amount',
            'Deposit Status',
            'Created By',
            'Updated By',
        );

        foreach($deposits as $deposit)
        {
            $deposit_array[] = array(
                'Deposit Date' => $deposit->deposit_date,
                'Bank Id' => $deposit->bank_id,
                'Deposit Code' => $deposit->deposit_code,
                'Amount' => $deposit->amount,
                'Deposit Status' =>$deposit->deposit_status,
                'Created By' => $deposit->created_by,
                'Updated By' => $deposit->updated_by,
            );
        }

        $this->exportExcel($deposit_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($deposits){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($deposits);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="deposits.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

}
