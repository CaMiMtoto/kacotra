<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int)request('row', 500);

        if ($row < 1 || $row > 500) {
            abort(400, 'The per-page parameter must be an integer between 1 and 500.');
        }

        $products = Product::with(['category', 'unit'])
            ->where('stock', '>', 0)
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        $customers = Customer::all()->sortBy('name');

        $carts = Cart::content();

        return view('pos.index', [
            'products' => $products,
            'customers' => $customers,
            'carts' => $carts,
        ]);
    }

    /**
     * Handle add product to cart.
     */
    public function addCartItem(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        $product = Product::where('id', $validatedData['id'])->first();

        if ($product->stock < 1) {
            return Redirect::back()->with('error', 'Product is less than security stock quantity!');
        } else {

            Cart::add([
                'id' => $validatedData['id'],
                'name' => $validatedData['name'],
                'qty' => 1,
                'price' => $validatedData['price']
            ]);

            return Redirect::back()->with('success', 'Product has been added to cart!');
            # code...
        }
    }

    /**
     * Handle update product in cart.
     */
    public function updateCartItem(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
            'id' => 'required|numeric'
        ];


        $validatedData = $request->validate($rules);

        $product = Product::where('id', $validatedData['id'])->first();

        if ($product->stock < $validatedData['qty']) {
            return Redirect::back()->with('error', 'Sorry, you cannot sell greater than product in stock!');
        } else {
            Cart::update($rowId, $validatedData['qty']);

            return Redirect::back()->with('success', 'Product has been updated from cart!');
        }


    }

    /**
     * Handle delete product from cart.
     */
    public function deleteCartItem(string $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Product has been deleted from cart!');
    }

    /**
     * Handle create an invoice.
     */
    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required|string'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $carts = Cart::content();

        return view('pos.create', [
            'customer' => $customer,
            'carts' => $carts
        ]);
    }
}
