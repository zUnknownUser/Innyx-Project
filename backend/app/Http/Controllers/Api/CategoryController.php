<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   
   public function index()
{
    return response()->json(
        Category::query()->select('id', 'name')->get()
    );
}

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json($category, 201);
    }

   
    public function show(string $id)
    {
        return Category::findOrFail($id);
    }

  
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json($category);
    }

    
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Categoria excluída com sucesso'
        ]);
    }
}