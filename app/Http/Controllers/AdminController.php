<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        return view("admin.index");
    }

   public function brands(Brand $brand){
    $brands = $brand->orderBy("id","desc")->paginate(15);
    return view('admin.brands', compact('brands'));
   }
}
