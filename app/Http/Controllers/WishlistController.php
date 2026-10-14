<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(Request $request): Response
    {
        $products = $request->user()
            ->wishlist()
            ->with('category:id,name')
            ->orderByPivot('created_at', 'desc')
            ->get();

        return Inertia::render('Wishlist', [
            'customerName' => $request->user()->name,
            'products' => $products,
            'wishlistCount' => $products->count(),
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlist()->syncWithoutDetaching($product->getKey());

        return back()->with('success', 'Product saved to your wishlist.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlist()->detach($product->getKey());

        return back()->with('success', 'Product removed from your wishlist.');
    }
}
