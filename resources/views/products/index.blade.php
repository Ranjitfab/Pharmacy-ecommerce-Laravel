@extends('layouts.app')

@section('title', 'Products | RxStock')

@section('content')
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">Products</h1>
            <p class="mb-0 mt-2 text-sm text-[#71807b]">Review stock levels, categories, and expiration dates.</p>
        </div>

        <a href="{{ route('products.create') }}" class="inline-flex w-fit items-center justify-center rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]">
            Add product
        </a>
    </div>

    <section aria-label="Product inventory" class="mt-8 overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
            <h2 class="m-0 text-sm font-semibold text-[#35443e]">Inventory list</h2>
            <span class="text-xs font-medium tabular-nums text-[#71807b]">{{ $products->count() }} products</span>
        </div>

        @if ($products->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-190 border-collapse text-left text-sm">
                    <thead class="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">Product</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Category</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Supplier</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Price</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Stock</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Expiration</th>
                            <th scope="col" class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1ef]">
                        @foreach ($products as $product)
                            <tr class="transition-colors hover:bg-[#fbfcfb]">
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-[#5f6c67]">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                <td class="px-5 py-4 text-[#5f6c67]">{{ $product->supplier?->name ?? 'Unassigned' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium tabular-nums text-[#35443e]">₱{{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->stock <= 5 ? 'bg-orange-50 text-orange-800' : 'bg-[#e7f2ef] text-[#27644f]' }}">
                                        {{ $product->stock }} {{ $product->stock <= 5 ? 'low stock' : 'in stock' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-[#5f6c67]">
                                    {{ $product->expiration_date ? \Illuminate\Support\Carbon::parse($product->expiration_date)->format('M j, Y') : 'Not set' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                        <a href="{{ route('products.show', $product) }}" class="font-medium text-[#237b65] underline-offset-4 hover:underline">View</a>
                                        <a href="{{ route('products.edit', $product) }}" class="font-medium text-[#43524c] underline-offset-4 hover:text-[#237b65] hover:underline">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border-0 bg-transparent p-0 font-medium text-[#b94343] hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-14 text-center">
                <h3 class="m-0 text-base font-semibold text-[#35443e]">No products yet</h3>
                <p class="mb-0 mt-2 text-sm text-[#71807b]">Add your first product to start tracking inventory.</p>
                <a href="{{ route('products.create') }}" class="mt-5 inline-flex rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline hover:bg-[#1b6957]">Add product</a>
            </div>
        @endif
    </section>
@endsection