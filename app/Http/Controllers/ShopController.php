<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query= Product::query()->where('status',true);

        $sortBy = $request->input('sort_by','newest');

        match($sortBy){
            'price_asc' => $query->orderBy('sale_price','asc'),
            'price_dsc' => $query->orderBy('sale_price','desc'),
            'featured' => $query->where('featured',true),
            default => $query->latest()
        };

        $per_page = $request->input('per_page','1');

        if($request->filled('brand')){
            $brandIds = $request->input('brand',[]);
            $query->whereIn('brand_id',$brandIds);
        }

        $products = $query->paginate($per_page)->withQueryString();

        $brands = Brand::withCount([
            'products'=> function ($query) {
                    $query->where('status', true);
                }
            ])->orderBy('name','asc')->get();

        return view('shop.index',compact('products','brands'));

    }

    public function productDetails(string $slug)
    {
        $product = Product::where('slug',$slug)->firstOrFail();

        $relatedProducts = Product::where('category_id',$product->category_id)
                            ->where('id','!=',$product->id)
                            ->where('status',true)
                            ->orderBy('created_at','desc')
                            ->take(8)
                            ->get();

        return view('shop.details',compact('product','relatedProducts'));

    }
}
