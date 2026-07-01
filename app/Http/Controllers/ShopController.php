<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

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

        $per_page = $request->input('per_page','12');

        if($request->filled('brand')){
            $brandIds = $request->input('brand',[]);
            $query->whereIn('brand_id',$brandIds);
        }

        if($request->filled('category')){
            $categoriesIds = $request->input('category',[]);
            $query->whereIn('category_id',$categoriesIds);
        }

        if($request->filled('min_price') && $request->filled('max_price')){
            $min_price = $request->input('min_price');
            $max_price = $request->input('max_price');
            $query->whereBetween('sale_price',[$min_price,$max_price]);
        }

        if($request->filled('search')){
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm){
                $q->where('name','like',"%{$searchTerm}%")
                ->orWhere('description','like',"%{$searchTerm}%");
            });
        }

        $products = $query->paginate($per_page)->withQueryString();
        // dd($query->toRawSql());
        $brands = Brand::withCount([
            'products'=> function ($query) {
                    $query->where('status', true);
                }
            ])->orderBy('name','asc')->get();


        $categories = Category::withCount([
            'products'=> function ($query) {
                    $query->where('status', true);
                }
            ])->orderBy('name')->get();
        return view('shop.index',compact('products','brands','categories'));

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
