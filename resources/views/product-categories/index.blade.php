@extends('layouts.app')

@section('title', 'Product categories | RxStock')

@section('content')
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">Product categories</h1>
            <p class="mb-0 mt-2 text-sm text-[#71807b]">Organize inventory and see how many products belong to each category.</p>
        </div>
        <a href="{{ route('product-categories.create') }}" class="inline-flex w-fit items-center justify-center rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]">
            Add category
        </a>
    </div>

    <section aria-label="Product categories" class="mt-8 overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
            <h2 class="m-0 text-sm font-semibold text-[#35443e]">Category list</h2>
            <span class="text-xs font-medium tabular-nums text-[#71807b]">{{ $categories->count() }} categories</span>
        </div>

        @if ($categories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-112.5 border-collapse text-left text-sm">
                    <thead class="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">Category</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Description</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Products</th>
                            <th scope="col" class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1ef]">
                        @foreach ($categories as $category)
                            <tr class="transition-colors hover:bg-[#fbfcfb]">
                                <td class="px-5 py-4">
                                    <a href="{{ route('product-categories.show', $category) }}" class="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">{{ $category->name }}</a>
                                </td>
                                <td class="max-w-md px-5 py-4 text-[#5f6c67]">{{ $category->description ?: 'No description' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-[#e7f2ef] px-2.5 py-1 text-xs font-semibold tabular-nums text-[#27644f]">{{ $category->products_count }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                        <a href="{{ route('product-categories.show', $category) }}" class="font-medium text-[#237b65] underline-offset-4 hover:underline">View</a>
                                        <a href="{{ route('product-categories.edit', $category) }}" class="font-medium text-[#43524c] underline-offset-4 hover:text-[#237b65] hover:underline">Edit</a>
                                        <form action="{{ route('product-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category? Its products will also be deleted.')">
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
                <h3 class="m-0 text-base font-semibold text-[#35443e]">No categories yet</h3>
                <p class="mb-0 mt-2 text-sm text-[#71807b]">Create a category before adding products.</p>
                <a href="{{ route('product-categories.create') }}" class="mt-5 inline-flex rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white no-underline hover:bg-[#1b6957]">Add category</a>
            </div>
        @endif
    </section>
@endsection