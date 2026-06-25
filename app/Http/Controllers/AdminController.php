<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Or use Imagick driver if available
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\DB;

use App\Models\Category;

DB::enableQueryLog();

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(){
        $query= Brand::query();
        if($search = request('search')){
            $query->where('name','LIKE',"%{$search}%");
        }

        if(request()->filled('status')){
            $query->where('status',request('status'));
        }

        $brands=  $query->orderBy('id','DESC')->paginate(10)->withQueryString();
        // dd(DB::getQueryLog());

        return view('admin.brands',compact('brands'));
    }

    public function brandAdd(){
        return view('admin.brand-add');
    }

    public function brandStore(Request $request){
        $request->validate([
                'name'=> 'required|string|max:255',
                'slug'=> 'nullable|string|max:255|unique:brands,slug',
                'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status'=> 'nullable|boolean',
            ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug =  $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $brand->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){
            $imageName = time().'_'.uniqid().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/brands'),$imageName);
            $brand->image = $imageName;

            // error:
            // Intervention\Image\Exceptions\FileNotFoundException
            // File "php3C26.tmp" contained in SplFileInfo was not found in directory "C:\wamp64\tmp"
            // $this->generateThumbImage($request->file('image'),$imageName,'uploads/brands');
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('success','Brand added successfully!');
    }

    public function generateThumbImage($file,$imageName,$folder,$width=124,$height=124){
        $thumbnailPath = public_path($folder.'/thumbnails');

        if(!file_exists($thumbnailPath)){
            mkdir($thumbnailPath,0755,true);
        }

        Image::decode($file)->resize($width,$height)->save($thumbnailPath.'/'.$imageName);
    }

    public function brandEdit($id){
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit',compact('brand'));
    }

    public function brandUpdate(Request $request,$id){
        $request->validate([
                'name'=> 'required|string|max:255',
                'slug'=> 'nullable|string|max:255|unique:brands,slug,'.$id,
                'image'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status'=> 'nullable|boolean',
            ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug =  $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $brand->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){

            if($brand->image && file_exists('uploads/brands/'.$brand->image)){
                @unlink(public_path('uploads/brands/'.$brand->image));
            }

            $imageName = time().'_'.uniqid().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/brands'),$imageName);
            $brand->image = $imageName;

        }

        $brand->save();

        return redirect()->route('admin.brands')->with('success','Brand updated successfully!');
    }

    public function brandDelete($id){

        $brand = Brand::findOrFail($id);

        if($brand->image && file_exists('uploads/brands/'.$brand->image)){
            @unlink(public_path('uploads/brands/'.$brand->image));
        }

        $brand->delete();

        return back()->with('success','Brand deleted successfully!');
    }

    public function categories(){
        $query= Category::query();
        if($search = request('search')){
            $query->where('name','LIKE',"%{$search}%");
        }

        if(request()->filled('status')){
            $query->where('status',request('status'));
        }

        $categories=  $query->orderBy('id','DESC')->paginate(10)->withQueryString();
        //  dd($query->toRawSql());
        return view('admin.categories',compact('categories'));
    }

    public function categoryAdd(){
        $parentCategories = Category::where('parent_id',null)->orderBy('name','ASC')->get();
        return view('admin.category-add',compact('parentCategories'));
    }

    public function categoryStore(Request $request){
        $request->validate([
                'name'=> 'required|string|max:255',
                'slug'=> 'nullable|string|max:255|unique:categories,slug',
                'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_id'=> 'nullable|exists:categories,id',
                'status'=> 'nullable|boolean',
            ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug =  $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $category->status = $request->has('status') ? 1 : 0;
        $category->parent_id = $request->parent_id;

        if($request->hasFile('image')){
            $imageName = time().'_'.uniqid().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/categories'),$imageName);
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('success','Category added successfully!');
    }

    public function categoryEdit($id){
        $category = Category::findOrFail($id);

        $parentCategories = Category::where('parent_id',null)->where('id','!=',$category->id)->orderBy('name','ASC')->get();
        return view('admin.category-edit',compact('category','parentCategories'));
    }

      public function categoryUpdate(Request $request,$id){
        $request->validate([
                'name'=> 'required|string|max:255',
                'slug'=> 'nullable|string|max:255|unique:categories,slug,'.$id,
                'image'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status'=> 'nullable|boolean',
                'parent_id'=> 'nullable|exists:categories,id',
            ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug =  $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $category->status = $request->has('status') ? 1 : 0;
        $category->parent_id = $request->parent_id;

        if($request->hasFile('image')){

            if($category->image && file_exists('uploads/categories/'.$category->image)){
                @unlink(public_path('uploads/categories/'.$category->image));
            }

            $imageName = time().'_'.uniqid().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/categories'),$imageName);
            $category->image = $imageName;

        }

        $category->save();

        return redirect()->route('admin.categories')->with('success','Category updated successfully!');
    }

    public function categoryDelete($id){

        $category = Category::findOrFail($id);

        if($category->image && file_exists('uploads/categories/'.$category->image)){
            @unlink(public_path('uploads/categories/'.$category->image));
        }

        $category->delete();

        return back()->with('success','Category deleted successfully!');
    }

}
