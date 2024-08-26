<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Exception;
use Carbon\Carbon;
use App\Models\Issue;
use App\Models\Department;
use App\Models\Expense;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\ExpenseDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ExpenseController extends Controller
{
    /**
     * Display an all expenses.
     */
    public function allExpenses()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $expenses = Expense::sortable()
            ->where('is_deleted',0)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate($row)
            ->appends(request()->query());

        return view('expenses.expenses', [
            'expenses' => $expenses
        ]);
    }

    /**
     * Display an all approved expenses.
     */
    public function approvedExpenses()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $expenses = Expense::where('expense_status', 1) // 1 = approved
            ->where('is_deleted',0)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('expenses.approved-expenses', [
            'expenses' => $expenses
        ]);
    }

    /**
     * Display an all pending expenses.
     */
    public function pendingExpenses()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $expenses = Expense::where('expense_status', 0) // 1 = pending
            ->where('is_deleted',0)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('expenses.pending-expenses', [
            'expenses' => $expenses
        ]);
    }

    /**
     * Display a expense details.
     */
    public function expenseDetails(String $expense_id)
    {
        $expense = Expense::with(['user_created','user_updated'])
            ->where('is_deleted',0)
            ->where('id', $expense_id)
            ->first();

        $expenseDetails = ExpenseDetails::with('issue')
            ->where('is_deleted',0)
            ->where('expense_id', $expense_id)
            ->orderByDesc('created_at')
            ->get();

        return view('expenses.details-expense', [
            'expense' => $expense,
            'expenseDetails' => $expenseDetails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createExpense()
    {
        return view('expenses.create-expense', [
            'issues' => Issue::where('is_deleted',0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeExpense(Request $request)
    {
        $rules = [
            'expense_date' => 'required|string',
            'comment' => 'nullable|string',
            'total_amount' => 'required|numeric'
        ];

        $expense_no = IdGenerator::generate([
            'table' => 'expenses',
            'field' => 'expense_no',
            'length' => 10,
            'prefix' => 'EXP-'
        ]);

        $validatedData = $request->validate($rules);

        $validatedData['expense_status'] = 0; // 0 = pending, 1 = approved
        $validatedData['expense_no'] = $expense_no;
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['created_at'] = Carbon::now();

        $expense_id = Expense::insertGetId($validatedData);

        // Create Expense Details
        $pDetails = array();
        $issues = count($request->issue_id);
        for ($i=0; $i < $issues; $i++) {
            $pDetails['expense_id'] = $expense_id;
            $pDetails['issue_id'] = $request->issue_id[$i];
            $pDetails['occurence'] = $request->occurence[$i];
            $pDetails['unitcost'] = $request->unitcost[$i];
            $pDetails['total'] = $request->total[$i];
            $pDetails['created_at'] = Carbon::now();

            ExpenseDetails::insert($pDetails);
        }

        return Redirect::route('expenses.allExpenses')->with('success', 'Expense has been created!');
    }

    /**
     * Handle update a status expense
     */
    public function updateExpense(Request $request)
    {
        $expense_id = $request->id;

        // after expense approved, add stock issue
        $issues = ExpenseDetails::where('expense_id', $expense_id)->get();

        foreach ($issues as $issue) {
            Issue::where('id', $issue->issue_id)
                    ->where('is_deleted',0)
                    ->update(['occurence' => DB::raw('occurence+'.$issue->occurence)]);
        }

        Expense::findOrFail($expense_id)
            ->update([
                'expense_status' => 1,
                'updated_by' => auth()->user()->id
            ]); // 1 = approved, 0 = pending

        /**
         * Record refunc to journal
         */
        $expense = Expense::where('id',$expense_id)
                            ->where('is_deleted',0)
                            ->first();

        $credit = $expense -> total_amount;
        $user = $expense -> created_by;
        $journal = Journal::whereDate('journal_date', Carbon::now()->format('Y-m-d'))
                            ->where('is_deleted',0)
                            ->first();

        $journals = Journal::where('is_deleted',0)->get();

        $comment = "Expense";

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
        $description = "Expense";
        $reference = $expense->expense_no;
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

        return Redirect::route('expenses.allExpenses')->with('success', 'Expense has been approved!');
    }

    /**
     * Handle delete a expense
     */
    public function deleteExpense(String $expense_id)
    {
        $expense = Expense::getSingle($expense_id);
        $expense->is_deleted = 1;
        $expense->save();

        ExpenseDetails::where('expense_id', $expense_id)->update([
            'is_deleted' => 1
        ]);

        return Redirect::route('expenses.allExpenses')->with('success', 'Expense has been deleted!');
    }

    /**
     * Display an all expenses.
     */
    public function dailyExpenseReport()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $expenses = Expense::where('expense_date', Carbon::now()->format('Y-m-d')) // 1 = approved
            ->where('is_deleted',0)
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('expenses.expenses', [
            'expenses' => $expenses
        ]);
    }

    /**
     * Show the form input date for expense report.
     */
    public function getExpenseReport()
    {
        return view('expenses.report-expense');
    }

    /**
     * Handle request to get expense report
     */
    public function exportExpenseReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        // $expenseDetails = DB::table('expenses')
        //     ->whereBetween('expenses.expense_date',[$sDate,$eDate])
        //     ->where('expenses.expense_status','1')
        //     ->join('expense_details', 'expenses.id', '=', 'expense_details.expense_id')
        //     ->get();

        $expenses = DB::table('expense_details')
            ->join('issues', 'expense_details.issue_id', '=', 'issues.id')
            ->join('expenses', 'expense_details.expense_id', '=', 'expenses.id')
            ->whereBetween('expenses.expense_date',[$sDate,$eDate])
            ->where('expenses.expense_status',1)
            ->where('expenses.is_deleted',0)
            ->select( 'expenses.expense_no', 'expenses.expense_date', 'expenses.supplier_id','issues.issue_code', 'issues.issue_name', 'expense_details.occurence', 'expense_details.unitcost', 'expense_details.total')
            ->get();


        $expense_array [] = array(
            'Date',
            'No Expense',
            'Supplier',
            'Issue Code',
            'Issue',
            'Occurence',
            'Unitcost',
            'Total',
        );

        foreach($expenses as $expense)
        {
            $expense_array[] = array(
                'Date' => $expense->expense_date,
                'No Expense' => $expense->expense_no,
                'Supplier' => $expense->supplier_id,
                'Issue Code' => $expense->issue_code,
                'Issue' => $expense->issue_name,
                'Occurence' => $expense->occurence,
                'Unitcost' => $expense->unitcost,
                'Total' => $expense->total,
            );
        }

        $this->exportExcel($expense_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($issues){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($issues);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="expense-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
