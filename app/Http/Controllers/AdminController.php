<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view("admin.index");
    }

    public function brands(Brand $brand)
    {
        $brands = $brand->orderBy("id", "desc")->paginate(15);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view("admin.brand-add");
    }

    public function brand_store(Request $request)
    {

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

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'unique:brands,slug'],
            'image' => ['mimes:jpeg,jpg,png', 'max:2048']
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {

            if (File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
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
        return redirect()->route('admin.brands')->with('status', 'Brand has been edited!');
    }

    public function GenerateBrandThumbnailsImage($imagePath, $imageName)
    {
        $destinationPath = public_path('uploads/brands'); // Path tujuan
        $img = Image::make($imagePath); // Membaca gambar yang diunggah
        $img->fit(124, 124, function ($constraint) { // Mengubah ukuran dan memotong gambar
            $constraint->upsize();
        });
        $img->save($destinationPath . '/' . $imageName); // Menyimpan gambar yang telah diubah ukurannya
    }

    public function brand_delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted!');
    }

    public function categories(Category $category)
    {
        $categories = $category->orderBy('id', 'ASC')->paginate(15);
        return view('admin.categories', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'unique:categories,slug'],
            'image' => ['mimes:jpeg,jpg,png', 'max:2048']
        ]);

        // Membuat objek Brand baru
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Mengunggah gambar jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image'); // Mengambil file gambar dari request
            $file_extension = $image->extension(); // Mendapatkan ekstensi file gambar
            $file_name = Carbon::now()->timestamp . '.' . $file_extension; // Membuat nama file unik
            $image->move(public_path('uploads/categories'), $file_name); // Memindahkan file gambar ke direktori yang ditentukan
            $category->image = $file_name; // Menyimpan nama file di atribut image dari objek category

            // Membuat thumbnail
            $this->GenerateCategoryThumbnailsImage(public_path('uploads/categories') . '/' . $file_name, $file_name);
        }

        // Menyimpan category ke database
        $category->save();

        // Mengalihkan dengan pesan sukses
        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'unique:categories,slug,' . $request->id],
            'image' => ['mimes:jpeg,jpg,png', 'max:2048']
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {

            if (File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
            $image = $request->file('image'); // Mengambil file gambar dari request
            $file_extension = $image->extension(); // Mendapatkan ekstensi file gambar
            $file_name = Carbon::now()->timestamp . '.' . $file_extension; // Membuat nama file unik
            $image->move(public_path('uploads/categories'), $file_name); // Memindahkan file gambar ke direktori yang ditentukan
            $category->image = $file_name; // Menyimpan nama file di atribut image dari objek Brand

            // Membuat thumbnail
            $this->GenerateBrandThumbnailsImage(public_path('uploads/categories') . '/' . $file_name, $file_name);
        }

        // Menyimpan Brand ke database
        $category->save();

        // Mengalihkan dengan pesan sukses
        return redirect()->route('admin.categories')->with('status', 'Brand has been edited!');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories/' . $category->image))) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted!');
    }

    public function GenerateCategoryThumbnailsImage($imagePath, $imageName)
    {
        $destinationPath = public_path('uploads/categories'); // Path tujuan
        $img = Image::make($imagePath); // Membaca gambar yang diunggah
        $img->fit(124, 124, function ($constraint) { // Mengubah ukuran dan memotong gambar
            $constraint->upsize();
        });
        $img->save($destinationPath . '/' . $imageName); // Menyimpan gambar yang telah diubah ukurannya
    }

    public function products()
    {
        $products = Product::orderBy('id', 'desc')->paginate(15);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('admin.product-add', compact('brands', 'categories'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'unique:products,slug'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'regular_price' => ['required', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'SKU' => ['required', 'string', 'max:255'],
            'stock_status' => ['required', 'in:instock,outofstock'],
            'featured' => ['required', 'boolean'],
            'quantity' => ['required', 'integer', 'min:0'],
            'image' => ['required', 'mimes:jpeg,jpg,png', 'max:2048'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $gallery_image = "";
        $counter = 1;

        if ($request->hasFile("images")) {
            $allowedFileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedFileExtension);
                if ($gcheck) {
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter++;
                }
            }
            $gallery_image = implode(',', $gallery_arr);
        }
        $product->images = $gallery_image;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }

    public function GenerateProductThumbnailsImage($imagePath, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails'); // Path tujuan
        $destinationPath = public_path('uploads/products'); // Path tujuan
        $img = Image::make($imagePath); // Membaca gambar yang diunggah
        $img->fit(540, 689, function ($constraint) { // Mengubah ukuran dan memotong gambar
            $constraint->upsize();
        })->save($destinationPath . '/' . $imageName); // Menyimpan gambar yang telah diubah ukurannya

        $img->fit(104, 104, function ($constraint) { // Mengubah ukuran dan memotong gambar
            $constraint->upsize();
        })->save($destinationPathThumbnail . '/' . $imageName); // Menyimpan gambar yang telah diubah ukurannya
    }
}
