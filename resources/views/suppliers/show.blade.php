@extends('layouts.app')

@section('title', $supplier->name . ' | RxStock')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                <a href="{{ route('suppliers.index') }}" class="text-[#237b65] no-underline hover:underline">Suppliers</a>
                <span class="px-1 text-[#9ba6a1]">/</span>
                Supplier details
            </p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">{{ $supplier->name }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('suppliers.index') }}" class="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] no-underline hover:bg-[#f8faf9]">Back to suppliers</a>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline hover:bg-[#1b6957]">Edit supplier</a>
        </div>
    </div>

    <section aria-label="Supplier contact details" class="mb-8 overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="border-b border-[#edf1ef] px-5 py-5 sm:px-7">
            <p class="mb-1 mt-0 text-xs font-semibold uppercase text-[#818c88]">Supplier profile</p>
            <h2 class="m-0 text-lg font-semibold text-[#26332f]">{{ $supplier->name }}</h2>
        </div>

        <dl class="m-0 grid gap-x-8 sm:grid-cols-2">
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Contact person</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">{{ $supplier->contact_person ?: 'Not set' }}</dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Email</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">
                    @if ($supplier->email)
                        <a href="mailto:{{ $supplier->email }}" class="text-[#237b65] underline-offset-4 hover:underline">{{ $supplier->email }}</a>
                    @else
                        Not set
                    @endif
                </dd>
            </div>
            <div class="border-b border-[#edf1ef] px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Phone</dt>
                <dd class="mb-0 mt-1 text-sm font-medium text-[#35443e]">
                    @if ($supplier->phone)
                        <a href="tel:{{ $supplier->phone }}" class="text-[#237b65] underline-offset-4 hover:underline">{{ $supplier->phone }}</a>
                    @else
                        Not set
                    @endif
                </dd>
            </div>
            <div class="px-5 py-4 sm:px-7">
                <dt class="text-xs font-semibold uppercase text-[#818c88]">Address</dt>
                <dd class="mb-0 mt-1 whitespace-pre-line text-sm leading-6 text-[#35443e]">{{ $supplier->address ?: 'Not set' }}</dd>
            </div>
        </dl>
    </section>

    <section aria-label="Supplier products" class="overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
            <h2 class="m-0 text-sm font-semibold text-[#35443e]">Supplied products</h2>
            <span class="text-xs font-medium tabular-nums text-[#71807b]">{{ $supplier->products->count() }} products</span>
        </div>

        @if ($supplier->products->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-112.5 border-collapse text-left text-sm">
                    <thead class="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">Product</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Category</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Stock</th>
                            <th scope="col" class="px-5 py-3 text-right font-semibold">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1ef]">
                        @foreach ($supplier->products as $product)
                            <tr class="transition-colors hover:bg-[#fbfcfb]">
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">{{ $product->name }}</a>
                                </td>
                                <td class="px-5 py-4 text-[#5f6c67]">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                <td class="px-5 py-4 tabular-nums text-[#5f6c67]">{{ $product->stock }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium tabular-nums text-[#35443e]">₱{{ number_format((float) $product->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="m-0 px-5 py-10 text-sm text-[#71807b]">No products are assigned to this supplier yet.</p>
        @endif
    </section>
@endsection