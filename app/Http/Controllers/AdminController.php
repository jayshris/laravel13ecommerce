<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Or use Imagick driver if available
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(){
        $brands= Brand::orderBy('id','DESC')->paginate(10);

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
                'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

}
