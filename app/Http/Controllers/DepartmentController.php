<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class DepartmentController extends Controller
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

        $departments = Department::filter(request(['search']))
          ->where('is_deleted',0)
          ->sortable()
          ->paginate($row)
          ->appends(request()->query());

        return view('departments.index', [
            'departments' => $departments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:departments,name',
            'slug' => 'required|unique:departments,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Department::create($validatedData);

        return Redirect::route('departments.index')->with('success', 'Department has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
      abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', [
            'department' => $department
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => 'required|unique:departments,name,'.$department->id,
            'slug' => 'required|alpha_dash|unique:departments,slug,'.$department->id,
        ];

        $validatedData = $request->validate($rules);

        Department::where('slug', $department->slug)->update($validatedData);

        return Redirect::route('departments.index')->with('success', 'Department has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->is_deleted = 1;
        $department->save();
        // Department::destroy($department->id);

        return Redirect::route('departments.index')->with('success', 'Department has been deleted!');
    }
}
