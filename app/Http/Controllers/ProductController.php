<?php

namespace App\Http\Controllers;

use App\models\Categories;
use App\models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Data Products";

        $datas = Products::with('category')->get();
        // return $datas;
        return view('products.index', compact('title', 'datas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Categories::orderBy('id', 'desc')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = [
            'category_id' => $request->category_id,
            'product_name'=> $request->product_name,
            'product_price'=> $request->product_price,
            'product_description'=> $request->product_description,
            'is_active'=> $request->is_active,
        ];
        
        if ($request->hasFile("product_photo")) {
            $photo = $request->file("product_photo")->store("product", "public");
            $data["product_photo"] = $photo;

        Products::create($data);
        toast('Data Added Successfully!','success');

        return redirect()->to('product');
    }}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit = Products::find($id);
        $categories = Categories::orderBy('id','desc')->get();
        return view('products.edit', compact('edit', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cara 1
        // Categories::where('id', $id)->update({
        //     'category_name => $request->category_name,
        // });
        // return redirect()->to('categories');
        
        $data = [
            'category_id' => $request->category_id,
            'product_name'=> $request->product_name,
            'product_price'=> $request->product_price,
            'product_description'=> $request->product_description,
            'is_active'=> $request->is_active,
        ];

        // cara 2
        $product = Products::find($id);
        if($request->hasFile('product_photo')){
            // Jika gambar sudah ada dan mau diubah maka gambar lama kita hapus di ganti oleh gambar baru
            if($product->product_photo){
                File::delete(public_path('storage/'. $product->photo));
            }

            $photo = $request->file('product_photo')->store('product', 'public');
            $data['product_photo'] = $photo;
            
        }
        
        $product->update($data);
        toast('Data Change Successfully!','success');

        return redirect()->to('product');


        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Products::find($id);
        File::delete(public_path('storage/' . $product->product_photo));
        $product->delete();
        toast('Data Deleted Successfully!','success');
        return redirect()->to('product');
    }
}
