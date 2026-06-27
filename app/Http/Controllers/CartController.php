<?php

namespace App\Http\Controllers;

use App\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function add_to_cart(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'proudct_id' => 'required|numeric',
                'name' => 'required|string',
                'quantity' => 'required|numeric|min:1',
                'price' => 'required|numeric|decimal:1,2|min:0',
            ]);
        } catch (ValidationException $e) {
            // Returns an associative array of all errors grouped by field
            $errors = $e->errors();

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        Cart::instance('cart')->add($request->proudct_id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');

        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update_cart(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        Cart::instance('cart')->update($request->rowId, $request->quantity);

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove_from_cart(string $rowId)
    {
        Cart::instance('cart')->remove($rowId);

        return back()->with('success', 'Product removed from cart successfully!');
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();

        return back()->with('success', 'Cart cleared successfully!');
    }

}
