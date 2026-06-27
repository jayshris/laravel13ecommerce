<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Cart;

class WishlistController extends Controller
{
    public function add_to_wishlist(Request $request)
    {
        $validatedData = $request->validate([
            'proudct_id' => 'required|numeric',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|decimal:1,2|min:0',
        ]);
        Cart::instance('wishlist')->add($request->proudct_id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');

        return back()->with('success', 'Product added to wishlist successfully!');
    }

    public function index()
    {
        return view('wishlist.index');
    }

    public function remove_from_wishlist(string $rowId)
    {
        Cart::instance('wishlist')->remove($rowId);

        return back()->with('success', 'Product removed from wishlist successfully!');
    }

    public function clear_wishlist()
    {
        Cart::instance('wishlist')->destroy();

        return back()->with('success', 'Wishlist cleared successfully!');
    }

    public function move_to_cart(string $rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId);

        Cart::instance('wishlist')->remove($rowId);

        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)->associate('App\Models\Product');

        return back()->with('success', 'Product moved to cart successfully!');
    }
}
