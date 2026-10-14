@extends('layouts.app')

@section('title', 'Edit product | RxStock')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">Edit product</h1>
            <p class="mb-0 mt-2 text-sm text-[#71807b]">Update details for {{ $product->name }}.</p>
        </div>
        <a href="{{ route('products.show', $product) }}" class="w-fit rounded-md px-3 py-2 text-sm font-medium text-[#53615c] no-underline hover:bg-white hover:text-[#237b65]">Cancel</a>
    </div>

    <section class="max-w-3xl rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm sm:p-7">
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-md border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="m-0 font-semibold">Please correct the highlighted fields.</p>
            </div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-sm font-medium text-[#35443e]">Product name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="255" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name') <p id="name-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="product_category_id" class="mb-1.5 block text-sm font-medium text-[#35443e]">Category</label>
                    <select id="product_category_id" name="product_category_id" required class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('product_category_id') aria-invalid="true" aria-describedby="category-error" @enderror>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('product_category_id') <p id="category-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supplier_id" class="mb-1.5 block text-sm font-medium text-[#35443e]">Supplier <span class="font-normal text-[#818c88]">(optional)</span></label>
                    <select id="supplier_id" name="supplier_id" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('supplier_id') aria-invalid="true" aria-describedby="supplier-error" @enderror>
                        <option value="">Unassigned</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p id="supplier-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="mb-1.5 block text-sm font-medium text-[#35443e]">Price</label>
                    <div class="flex overflow-hidden rounded-md border border-[#d8e0dc] focus-within:border-[#237b65] focus-within:ring-2 focus-within:ring-[#237b65]/15">
                        <span class="border-r border-[#d8e0dc] bg-[#f8faf9] px-3 py-2.5 text-sm text-[#71807b]">₱</span>
                        <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required class="min-w-0 flex-1 border-0 bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none" @error('price') aria-invalid="true" aria-describedby="price-error" @enderror>
                    </div>
                    @error('price') <p id="price-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stock" class="mb-1.5 block text-sm font-medium text-[#35443e]">Stock</label>
                    <input id="stock" type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}" required class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('stock') aria-invalid="true" aria-describedby="stock-error" @enderror>
                    @error('stock') <p id="stock-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="dosage" class="mb-1.5 block text-sm font-medium text-[#35443e]">Dosage <span class="font-normal text-[#818c88]">(optional)</span></label>
                    <input id="dosage" type="text" name="dosage" value="{{ old('dosage', $product->dosage) }}" placeholder="e.g. 500 mg" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('dosage') aria-invalid="true" aria-describedby="dosage-error" @enderror>
                    @error('dosage') <p id="dosage-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="strength" class="mb-1.5 block text-sm font-medium text-[#35443e]">Strength <span class="font-normal text-[#818c88]">(optional)</span></label>
                    <input id="strength" type="text" name="strength" value="{{ old('strength', $product->strength) }}" placeholder="e.g. 30 tablets" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('strength') aria-invalid="true" aria-describedby="strength-error" @enderror>
                    @error('strength') <p id="strength-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expiration_date" class="mb-1.5 block text-sm font-medium text-[#35443e]">Expiration date <span class="font-normal text-[#818c88]">(optional)</span></label>
                    <input id="expiration_date" type="date" name="expiration_date" value="{{ old('expiration_date', $product->expiration_date) }}" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('expiration_date') aria-invalid="true" aria-describedby="expiration-error" @enderror>
                    @error('expiration_date') <p id="expiration-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-2 rounded-md border border-[#d8e0dc] bg-[#f8faf9] px-3 py-2.5 text-sm font-medium text-[#35443e]">
                        <input type="checkbox" name="requires_prescription" value="1" @checked(old('requires_prescription', $product->requires_prescription))>
                        Requires prescription
                    </label>
                    @error('requires_prescription') <p class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-[#35443e]">Description <span class="font-normal text-[#818c88]">(optional)</span></label>
                    <textarea id="description" name="description" rows="4" class="w-full resize-y rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-[#edf1ef] pt-5">
                <a href="{{ route('products.show', $product) }}" class="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] no-underline hover:bg-[#f8faf9]">Cancel</a>
                <button type="submit" class="rounded-md border-0 bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]">Save changes</button>
            </div>
        </form>
    </section>
@endsection