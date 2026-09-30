@extends('layouts.app')

@section('title', $productCategory->name . ' | RxStock')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                <a href="{{ route('product-categories.index') }}" class="text-[#237b65] no-underline hover:underline">Categories</a>
                <span class="px-1 text-[#9ba6a1]">/</span>
                Category details
            </p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">{{ $productCategory->name }}</h1>
            <p class="mb-0 mt-2 text-sm text-[#71807b]">{{ $productCategory->description ?: 'No description provided.' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('product-categories.index') }}" class="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] no-underline hover:bg-[#f8faf9]">Back to categories</a>
            <a href="{{ route('product-categories.edit', $productCategory) }}" class="rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline hover:bg-[#1b6957]">Edit category</a>
        </div>
    </div>

    <section aria-label="Products in category" class="overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
            <h2 class="m-0 text-sm font-semibold text-[#35443e]">Products in this category</h2>
            <span class="text-xs font-medium tabular-nums text-[#71807b]">{{ $productCategory->products->count() }} products</span>
        </div>

        @if ($productCategory->products->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-190 border-collapse text-left text-sm">
                    <thead class="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">Product</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Supplier</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Stock</th>
                            <th scope="col" class="px-5 py-3 text-right font-semibold">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1ef]">
                        @foreach ($productCategory->products as $product)
                            <tr class="transition-colors hover:bg-[#fbfcfb]">
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">{{ $product->name }}</a>
                                </td>
                                <td class="px-5 py-4 text-[#5f6c67]">{{ $product->supplier?->name ?? 'Unassigned' }}</td>
                                <td class="px-5 py-4 tabular-nums text-[#5f6c67]">{{ $product->stock }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium tabular-nums text-[#35443e]">₱{{ number_format((float) $product->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-10">
                <p class="m-0 text-sm text-[#71807b]">There are no products in this category yet.</p>
            </div>
        @endif
    </section>
@endsection