<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with(['category', 'supplier'])->latest()->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'baseUrl' => route('products.index'),
            'success' => session('success'),
        ]);
    }

    public function create(): Response
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return Inertia::render('Products/Form', [
            'item' => null,
            'baseUrl' => route('products.index'),
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
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

    public function show(Product $product): Response
    {
        $product->load(['category', 'supplier']);

        return Inertia::render('Products/Show', [
            'item' => $product,
            'baseUrl' => route('products.index'),
        ]);
    }

    public function edit(Product $product): Response
    {
        $categories = ProductCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return Inertia::render('Products/Form', [
            'item' => $product,
            'baseUrl' => route('products.index'),
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
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
