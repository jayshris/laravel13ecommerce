<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\ImageFormat;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ProductController extends Controller
{
    public function products()
    {
        $products = Product::with('category', 'brand')->orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.products', compact('products'));
    }

    public function productAdd()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string|max:255',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'SKU' => 'required|string|max:100|unique:products,SKU',
            'stock_status' => 'required|in:instock,outofstock',
            'quantity' => 'required|integer|min:0',
            'information' => 'nullable|string|max:255',
            'featured' => 'boolean',
            'status' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->information = $request->information;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->status = $request->status;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$request->image->extension();
            $this->resizeAndSaveImage($image, $imageName ,'uploads/products/',570,604);
            $this->resizeAndSaveImage($image, $imageName , 'uploads/products/thumbnails/',270,303);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $gallery_imgs = '';$counter = 1;
        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg','png','jpeg','webp'];
            $files = $request->file('images');

            foreach($files as $file){
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension,$allowedfileExtion);

                if($gcheck){
                    $gimageName = $current_timestamp.'_'.$counter.'.'.$gextension;
                    $this->resizeAndSaveImage($file, $gimageName ,'uploads/products/',570,604);
                    $this->resizeAndSaveImage($file, $gimageName ,'uploads/products/thumbnails/',270,303);
                    array_push($gallery_arr,$gimageName);
                    $counter = $counter + 1;
                }
            }
            $gallery_imgs = implode(',',$gallery_arr);
        }
        $product->images = $gallery_imgs;

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully!');
    }

    function resizeAndSaveImage($file, $imageName ,$filePath,$width = 570,$height = 604){

        // 1. Store original temporarily on local filesystem
        $tempPath = $file->store('temp');
        $absoluteTempPath = storage_path('app/private/' . $tempPath);

        // 2. Setup the final destination details
        $finalPath = public_path($filePath. $imageName);

        // 3. Load, resize, and save directly to public storage
        Image::load($absoluteTempPath)
            ->fit(Fit::Crop, $width, $height)  // Forces exact 300x300 center crop
            // ->format(ImageFormat::Webp)
            ->optimize()
            ->save($finalPath);

        // 4. Clean up temporary un-resized file
        Storage::delete($tempPath);
    }
}
