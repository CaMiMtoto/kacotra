<?php

namespace App\Http\Controllers;

use App\Models\Method;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class MethodController extends Controller
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

        $methods = Method::filter(request(['search']))
            ->where('is_deleted',0)
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('methods.index', [
            'methods' => $methods,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:methods,name',
            'slug' => 'required|unique:methods,slug|alpha_dash',
            'code' => 'required|unique:methods,code',
        ];

        $validatedData = $request->validate($rules);

        Method::create($validatedData);

        return Redirect::route('methods.index')->with('success', 'Method has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Method $method)
    {
      abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Method $method)
    {
        return view('methods.edit', [
            'method' => $method
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Method $method)
    {
        $rules = [
            'name' => 'required|unique:methods,name,'.$method->id,
            'slug' => 'required|alpha_dash|unique:methods,slug,'.$method->id,
            'code' => 'required|unique:methods,code,'.$method->id,
        ];

        $validatedData = $request->validate($rules);

        Method::where('slug', $method->slug)->update($validatedData);

        return Redirect::route('methods.index')->with('success', 'Method has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Method $method)
    {
        $method->is_deleted = 1;
        $method->save();
        // Method::destroy($method->id);

        return Redirect::route('methods.index')->with('success', 'Method has been deleted!');
    }
}
