<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view("admin.index");
    }

   public function brands(Brand $brand){
    $brands = $brand->orderBy("id","desc")->paginate(15);
    return view('admin.brands', compact('brands'));
   }

   public function add_brand(){
    return view("admin.brand-add");
   }

   public function brand_store(Request $request){
   
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'unique:brands,slug'],
            'image' => ['mimes:jpeg,jpg,png', 'max:2048']
        ]);

        // Membuat objek Brand baru
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Mengunggah gambar jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image'); // Mengambil file gambar dari request
            $file_extension = $image->extension(); // Mendapatkan ekstensi file gambar
            $file_name = Carbon::now()->timestamp . '.' . $file_extension; // Membuat nama file unik
            $image->move(public_path('uploads/brands'), $file_name); // Memindahkan file gambar ke direktori yang ditentukan
            $brand->image = $file_name; // Menyimpan nama file di atribut image dari objek Brand

            // Membuat thumbnail
            $this->GenerateBrandThumbnailsImage(public_path('uploads/brands') . '/' . $file_name, $file_name);
        }

        // Menyimpan Brand ke database
        $brand->save();

        // Mengalihkan dengan pesan sukses
        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
   }

   public function GenerateBrandThumbnailsImage($imagePath, $imageName){
        $destinationPath = public_path('uploads/brands'); // Path tujuan
        $img = Image::make($imagePath); // Membaca gambar yang diunggah
        $img->fit(124, 124, function ($constraint) { // Mengubah ukuran dan memotong gambar
            $constraint->upsize();
        });
        $img->save($destinationPath . '/' . $imageName); // Menyimpan gambar yang telah diubah ukurannya
   }

}
