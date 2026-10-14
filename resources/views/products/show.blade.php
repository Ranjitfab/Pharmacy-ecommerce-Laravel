@extends('layouts.app')

@section('title', $product->name . ' | RxStock')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                <a href="{{ route('products.index') }}" class="text-[#237b65] no-underline hover:underline">Products</a>
                <span class="px-1 text-[#9ba6a1]">/</span>
                Product details
            </p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">{{ $product->name }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('products.index') }}" class="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] no-underline hover:bg-[#f8faf9]">Back to products</a>
            <a href="{{ route('products.edit', $product) }}" class="rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline hover:bg-[#1b6957]">Edit product</a>
        </div>
    </div>

    <section aria-label="Product details" class="overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="border-b border-[#edf1ef] px-5 py-5 sm:px-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="mb-1 mt-0 text-xs font-semibold uppercase text-[#818c88]">Product</p>
                    <h2 class="m-0 text-lg font-semibold text-[#26332f]">{{ $product->name }}</h2>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->stock <= 5 ? 'bg-orange-50 text-orange-800' : 'bg-[#e7f2ef] text-[#27644f]' }}">
                    {{ $product->stock }} {{ $product->stock <= 5 ? 'units · low stock' : 'units in stock' }}
                </span>
            </div>
        </div>

        <dl class="m-0 grid gap-x-8 sm:grid-cols-2">
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Category</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">{{ $product->category?->name ?? 'Uncategorized' }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Supplier</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">{{ $product->supplier?->name ?? 'Unassigned' }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Price</dt>
                <dd class="mb-0 mt-1 text-sm font-medium tabular-nums text-[#35443e]">₱{{ number_format((float) $product->price, 2) }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Dosage</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">{{ $product->dosage ?: 'Not specified' }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Strength</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">{{ $product->strength ?: 'Not specified' }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Stock on hand</dt>
                <dd class="mb-0 mt-1 text-sm font-medium tabular-nums text-[#35443e]">{{ $product->stock }} units</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Expiration date</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">
                    {{ $product->expiration_date ? \Illuminate\Support\Carbon::parse($product->expiration_date)->format('M j, Y') : 'Not set' }}
                </dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Prescription status</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">
                    {{ $product->requires_prescription ? 'Prescription required' : 'No prescription required' }}
                </dd>
            </div>
            <div class="px-5 py-5 sm:col-span-2 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Description</dt>
                <dd class="mb-0 mt-2 whitespace-pre-line text-sm leading-6 text-[#53615c]">{{ $product->description ?: 'No description provided.' }}</dd>
            </div>
        </dl>
    </section>
@endsection