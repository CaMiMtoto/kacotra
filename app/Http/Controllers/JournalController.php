<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Journal;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Recovery;
use App\Models\Deposit;
use App\Models\Due;
use App\Models\Expense;
use App\Models\ExpenseDetails;
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

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 100);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $journals = Journal::with('user')
                ->filter(request(['search']))
                ->where('is_deleted',0)
                ->orderByDesc('id')
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        $totalDebit = $journals -> sum('debit');
        $totalCredit = $journals -> sum('credit');
        $totalDue = $journals -> sum('due');
        $totalRefund = $journals -> sum('refund');

        return view('journals.index', [
            'journals' => $journals,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'totalDue' => $totalDue,
            'totalRefund' => $totalRefund,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('journals.create', [
            'users' => User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
            'opening' => 'required|integer',
            'debit' => 'required|integer',
            'credit' => 'required|integer',
            'due' => 'required|integer',
            'refund' => 'required|integer',
            'balance' => 'required|integer',
            'comment' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);


        Journal::create($validatedData);

        return Redirect::route('journals.index')->with('success', 'Journal has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Journal $journal)
    {

        return view('journals.show', [
            'journal' => $journal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Journal $journal)
    {
        return view('journals.edit', [
            'users' => User::all(),
            'journal' => $journal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Journal $journal)
    {
        $rules = [
            'user_id' => 'required|integer',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
            'opening' => 'required|integer',
            'debit' => 'required|integer',
            'credit' => 'required|integer',
            'due' => 'required|integer',
            'refund' => 'required|integer',
            'balance' => 'required|integer',
            'comment' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);

        Journal::where('id', $journal->id)->update($validatedData);

        return Redirect::route('journals.index')->with('success', 'Journal has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteJournal($id)
    {
        $journal = Journal::getSingle($id);
        $journal -> is_deleted = 1;
        $journal -> save();

        if(Str::contains($journal->reference, 'INV-'))
        {
            $order = Order::where('invoice_no',$journal->reference)
                        ->first();
            
            if ($journal->description == "Sales") {
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
            
            if (Str::contains($journal->description, 'Refund')) {
                $refund = Refund::where('order_id', $order->id)
                                ->where('is_deleted',0)
                                ->get()
                                ->latest();
                                        
                    $refund -> is_deleted = 1;
                    $refund -> save();
                    
            } 
            
            if (Str::contains($journal->description,'Payment of due invoice')) {
                $dues = Due::where('order_id', $order->id)
                            ->get(); 
                foreach ($dues as $due) {
                    $due -> is_deleted = 1;
                    $due -> save();
                }
            }
            
        }

        if(Str::contains($journal->reference, 'PRS-'))
        {
            $purchase = Purchase::where('purchase_no',$journal->reference)
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

        if(Str::contains($journal->reference, 'EXP'))
        {
            $expense = Expense::where('expense_no',$journal->reference)
                        ->first();
            $details = ExpenseDetails::where('expense_id', $expense->id)
                                    ->get();
            foreach($details as $detail)
            {
                $payment = $detail->total;

                $detail -> is_deleted = 1;
                $detail -> save();

                $old_amount = Expense::where('id',$detail->expense_id)
                                    ->first();
                $new_amount = $old_amount->total_amount + $payment;

                Expense::where('id',$detail->expense_id)
                        ->update([
                            'total_amount' => $new_amount
                        ]);
            }
        }

        if(Str::contains($journal->reference, 'DEP'))
        {
            Deposit::where('deposit_code',$journal->reference)
                    ->update([
                        'is_deleted' => 1
                    ]);

        }
        

        // Journal::destroy($journal->id);

        return Redirect::route('journals.index')->with('success', 'Journal record has been deleted!');
    }

    /**
     * Handle export data journals.
     */
    public function import()
    {
        return view('journals.import');
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
                    'journal_date' => $sheet->getCell( 'A' . $row )->getValue(),
                    'user_id' => $sheet->getCell( 'B' . $row )->getValue(),
                    'description' => $sheet->getCell( 'C' . $row )->getValue(),
                    'reference' => $sheet->getCell( 'D' . $row )->getValue(),
                    'debit' => $sheet->getCell( 'E' . $row )->getValue(),
                    'credit' => $sheet->getCell( 'F' . $row )->getValue(),
                    'due' => $sheet->getCell( 'G' . $row )->getValue(),
                    'refund' => $sheet->getCell( 'H' . $row )->getValue(),
                    'balance' =>$sheet->getCell( 'I' . $row )->getValue(),
                    'comment' =>$sheet->getCell( 'J' . $row )->getValue(),
                ];
                $startcount++;
            }

            Journal::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('journals.index')->with('error', 'There was a problem uploading the data!');
        }
        return Redirect::route('journals.index')->with('success', 'Data journal has been imported!');
    }

    /**
     * Handle export data journals.
     */
    function export()
    {
        $journals = Journal::all()->sortBy('journal_date');

        $journal_array [] = array(
            'Date',
            'Created by',
            'Description',
            'Reference',
            'Debit (+)',
            'Credit (-)',
            'Due',
            'Refund',
            'Balance',
            'Comment',
        );

        foreach($journals as $journal)
        {
            $journal_array[] = array(
                'Date' => $journal->journal_date,
                'Created by' => $journal->user->name,
                'Description' => $journal->description,
                'Reference' => $journal->reference,
                'Debit (+)' => $journal->debit,
                'Credit (-)' =>$journal->credit,
                'Due' =>$journal->due,
                'Refund' => $journal->refund,
                'Balance' =>$journal->balance,
                'Comment' => $journal->comment,
            );
        }

        $this->exportExcel($journal_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($journals)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($journals);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="journals.xls"');
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
    public function dailyJournalReport()
    {
        $row = (int) request('row', 100);

        if ($row < 1 || $row> 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
            }

        $journals = DB::table('journals')
            -> join('users', 'users.id', '=', 'journals.user_id')
            -> select(
                'journals.journal_date as j_date',
                'journals.opening as opening',
                DB::raw('SUM(journals.debit) as debit'),
                DB::raw('SUM(journals.credit) as credit'),
                DB::raw('SUM(journals.due) as due'),
                DB::raw('SUM(journals.refund) as refund'),
                DB::raw('SUM(journals.balance) as balance')
            )
            -> whereDate('journals.created_at', '=', Carbon::now() -> format('Y-m-d'))
            -> orderByDesc('journals.created_at')
            -> groupBy('j_date')
            -> paginate($row)
            -> appends(request()->query());

        $totalDebit = $journals -> sum('debit');
        $totalCredit = $journals -> sum('credit');
        $totalDue = $journals -> sum('due');
        $totalRefund = $journals -> sum('refund');

        $title = "Journal report of ". Carbon::now() -> format('d M, Y');
        return view('journals.index', [
            'title' => $title,
            'journals' => $journals,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'totalDue' => $totalDue,
            'totalRefund' => $totalRefund,
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
            $title = "Journals from " . Carbon::parse($fromDate)->format('M d, Y') . " to " .
            Carbon::parse($toDate) ->format('M d, Y');
        } elseif (Carbon::parse($fromDate) == Carbon::parse($toDate))
        {
            $argument = $fromDate;
            $logic = "whereDate";
            $title = "Journals of " . Carbon::parse($fromDate)->format('M d, Y');
        } else
        {
            $argument = [$toDate, $fromDate];
            $logic = "whereBetween";
            $title = "Journals from " . Carbon::parse($toDate)->format('M d, Y') . " to " .
            Carbon::parse($fromDate) ->format('M d, Y');
        }

        $journals = Journal::with(['user'])
                ->$logic('journal_date',$argument)
                ->where('is_deleted',0)
                ->orderByDesc('id')
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        $totalDebit = $journals -> sum('debit');
        $totalCredit = $journals -> sum('credit');
        $totalDue = $journals -> sum('due');
        $totalRefund = $journals -> sum('refund');

        return view('journals.journals', [
            'journals' => $journals,
            'title' => $title,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'totalDue' => $totalDue,
            'totalRefund' => $totalRefund,
        ]);
    }

}
