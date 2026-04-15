<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

   
    public function index(Request $request)
    {
        // 🔐 ACL
        $this->authorize('viewAny', Product::class);

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(10);

        return response()->json($products);
    }

    
    public function store(Request $request)
    {
        // 🔐 ACL
        $this->authorize('create', Product::class);

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:200',
            'price' => 'required|numeric|gt:0',
            'expiration_date' => 'required|date|after_or_equal:today',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'expiration_date' => $request->expiration_date,
            'image' => $imagePath,
            'category_id' => $request->category_id,
        ]);

        return response()->json($product->load('category'), 201);
    }

    
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

     
        $this->authorize('view', $product);

        return response()->json($product);
    }

 
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

       
        $this->authorize('update', $product);

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:200',
            'price' => 'required|numeric|gt:0',
            'expiration_date' => 'required|date|after_or_equal:today',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'expiration_date' => $request->expiration_date,
            'category_id' => $request->category_id,
            'image' => $product->image,
        ]);

        return response()->json($product->load('category'));
    }

   
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        //  ACL
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'message' => 'Produto excluído com sucesso'
        ]);
    }
}