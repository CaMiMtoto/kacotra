<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Unit;
use App\Models\Issue;
use App\Models\Department;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
// use Picqer\Barcode\BarcodeGeneratorHTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class IssueController extends Controller
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

        $issues = Issue::with(['unit'])
                ->where('is_deleted',0)
                ->orderByDesc('created_at')
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        return view('issues.index', [
            'issues' => $issues,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('issues.create', [
            'units' => Unit::where('is_deleted',0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $issue_code = IdGenerator::generate([
            'table' => 'issues',
            'field' => 'issue_code',
            'length' => 6,
            'prefix' => 'ISS'
        ]);

        $rules = [
            'issue_name' => 'required|string',
            'unit_id' => 'required|integer',
            'occurence' => 'required|integer',
            'cost' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        // Save issue code value
        $validatedData['issue_code'] = $issue_code;

        /**
         * Handle upload image
         */
        /*
        if ($file = $request->file('issue_image')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/issues/';
        */
            /**
             * Upload an image to Storage
             */
            /*
            $file->storeAs($path, $fileName);
            $validatedData['issue_image'] = $fileName;
        }
         */

        Issue::create($validatedData);

        return Redirect::route('issues.index')->with('success', 'Issue has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        // Generate a barcode
        /*
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($issue->issue_code, $generator::TYPE_CODE_128);
        */
        return view('issues.show', [
            'issue' => $issue,
            /* 'barcode' => $barcode, */
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {
        return view('issues.edit', [
            'units' => Unit::where('is_deleted',0)->get(),
            'issue' => $issue
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Issue $issue)
    {
        $rules = [
            'issue_name' => 'required|string',
            'unit_id' => 'required|integer',
            'occurence' => 'required|integer',
            'cost' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload an image
         */
        /*
        if ($file = $request->file('issue_image')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/issues/';
         */
            /**
             * Delete photo if exists.
             */

            /*
            if($issue->issue_image){
                Storage::delete($path . $issue->issue_image);
            }
             */
            /**
             * Store an image to Storage
             */
            /*
            $file->storeAs($path, $fileName);
            $validatedData['issue_image'] = $fileName;
        }
         */

        Issue::where('id', $issue->id)->update($validatedData);

        return Redirect::route('issues.index')->with('success', 'Issue has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue)
    {
        $issue->is_deleted = 1;
        $issue->save();
        /**
         * Delete photo if exists.
         */
        /*
        if($issue->issue_image){
            Storage::delete('public/issues/' . $issue->issue_image);
        }
         */
        // Issue::destroy($issue->id);

        return Redirect::route('issues.index')->with('success', 'Issue has been deleted!');
    }

    /**
     * Handle export data issues.
     */
    public function import()
    {
        return view('issues.import');
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
                    'issue_name' => $sheet->getCell( 'A' . $row )->getValue(),
                    'unit_id' => $sheet->getCell( 'B' . $row )->getValue(),
                    'issue_code' => $sheet->getCell( 'C' . $row )->getValue(),
                    'occurence' => $sheet->getCell( 'D' . $row )->getValue(),
                    'cost' => $sheet->getCell( 'F' . $row )->getValue(),
                ];
                $startcount++;
            }

            Issue::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('issues.index')->with('error', 'There was a problem uploading the data!');
        }
        return Redirect::route('issues.index')->with('success', 'Data issue has been imported!');
    }

    /**
     * Handle export data issues.
     */
    function export(){
        $issues = Issue::where('is_deleted',0)->get()->sortBy('issue_name');

        $issue_array [] = array(
            'Issue Name',
            'Unit Id',
            'Issue Code',
            'Occurence',
            'Cost',
        );

        foreach($issues as $issue)
        {
            $issue_array[] = array(
                'Issue Name' => $issue->issue_name,
                'Unit Id' => $issue->unit_id,
                'Issue Code' => $issue->issue_code,
                'Occurence' => $issue->occurence,
                'Cost' =>$issue->cost,
            );
        }

        $this->exportExcel($issue_array);
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
            header('Content-Disposition: attachment;filename="issues.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

}
