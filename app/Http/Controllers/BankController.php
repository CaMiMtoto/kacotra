<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per_page parameter must be an integer between 1 and 100.');
        }

        $banks = Bank::filter(request(['search']))
          ->where('is_deleted',0)
          ->sortable()
          ->paginate($row)
          ->appends(request()->query());

        return view('banks.index', [
            'banks' => $banks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:banks,name',
            'slug' => 'required|unique:banks,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Bank::create($validatedData);

        return Redirect::route('banks.index')->with('success', 'Bank has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
      abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('banks.edit', [
            'bank' => $bank
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $rules = [
            'name' => 'required|unique:banks,name,'.$bank->id,
            'slug' => 'required|alpha_dash|unique:banks,slug,'.$bank->id,
        ];

        $validatedData = $request->validate($rules);

        Bank::where('slug', $bank->slug)->update($validatedData);

        return Redirect::route('banks.index')->with('success', 'Bank has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->is_deleted = 1;
        $bank->save();
        // Bank::where('id',$bank->id)->update($bank);

        return Redirect::route('banks.index')->with('success', 'Bank has been deleted!');
    }
}
