<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $products = Product::with(['category', 'supplier'])->latest()->get();

        return view('products.index', compact('products'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:255',
            'requires_prescription' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully');
    }

    // Display the specified resource
    public function show(Product $product)
    {
        $product->load(['category', 'supplier']);

        return view('products.show', compact('product'));
    }

    // Show the form for editing a specified resource
    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:255',
            'requires_prescription' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    // Remove specified resource from storage
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
