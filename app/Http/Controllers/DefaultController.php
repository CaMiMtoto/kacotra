<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Product;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    // Get all products by category
    public function GetProducts(Request $request){
        $category_id = $request->category_id;
        $allProduct = Product::where('category_id',$category_id)->get();

        return response()->json($allProduct);
    }
    // Get all products by department
    public function GetIssues(Request $request){
        $department_id = $request->department_id;
        $allIssue = Issue::where('department_id',$department_id)->get();

        return response()->json($allIssue);
    }
}
