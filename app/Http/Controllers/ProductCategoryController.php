<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{

    //Display a listing of the resource
    public function index()
    {
        $categories = ProductCategory::withCount('products')->latest()->get();

        return view('product-categories.index', compact('categories'));
    }

    //Show the form for creating a new resource
    public function create()
    {
        return view('product-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ProductCategory::create($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category created successfully');
    }

    //Display the specified resource
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load('products.supplier');

        return view('product-categories.show', compact('productCategory'));
    }

    //Show the form for editing a specified resource
    public function edit(ProductCategory $productCategory)
    {
        return view('product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $productCategory->update($validated);
        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category updated successfully');
    }

    //Remove specified resource from storage
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
