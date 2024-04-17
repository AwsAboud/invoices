<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:عرض-منتج', ['only' => ['index']]);
        $this->middleware('permission:اضافة-منتج', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل-منتج', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف-منتج', ['only' => ['destroy']]);
    }


    public function index()
    {
        return view('products.index',[
            'products' => Product::with('section')->get(),
            'sections' => Section::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        Product::create([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'section_id' => $request->section_id,

        ]);
        return redirect()->back()->with(['add' => 'تم اضافة المنتج بنجاح ']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request)
    {
      $product = Product::findOrFail($request->id);
      $product->update([
        'product_name' => $request->product_name,
        'description' => $request->description,
        'section_id' => $request->section_id,
      ]);
      return redirect()->back()->with(['update' => 'تم تعديل المنتج بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Product::findOrFail($request->id)->delete();
        return redirect()->back()->with(['delete' => 'تم حذف المنتج بنجاح']);
    }
}
