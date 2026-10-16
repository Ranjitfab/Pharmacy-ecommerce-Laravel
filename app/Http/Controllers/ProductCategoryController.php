<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = ProductCategory::withCount('products')->latest()->get();

        return Inertia::render('ProductCategories/Index', [
            'categories' => $categories,
            'baseUrl' => route('product-categories.index'),
            'success' => session('success'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ProductCategories/Form', [
            'item' => null,
            'baseUrl' => route('product-categories.index'),
        ]);
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

    public function show(ProductCategory $productCategory): Response
    {
        $productCategory->load('products.supplier');

        return Inertia::render('ProductCategories/Show', [
            'item' => $productCategory,
            'baseUrl' => route('product-categories.index'),
            'productsUrl' => route('products.index'),
        ]);
    }

    public function edit(ProductCategory $productCategory): Response
    {
        return Inertia::render('ProductCategories/Form', [
            'item' => $productCategory,
            'baseUrl' => route('product-categories.index'),
        ]);
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

    // Remove specified resource from storage
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
