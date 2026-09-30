@extends('layouts.app')

@section('title', 'Edit supplier | RxStock')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
            <h1 class="m-0 text-2xl font-bold text-[#26332f]">Edit supplier</h1>
            <p class="mb-0 mt-2 text-sm text-[#71807b]">Update contact details for {{ $supplier->name }}.</p>
        </div>
        <a href="{{ route('suppliers.show', $supplier) }}" class="w-fit rounded-md px-3 py-2 text-sm font-medium text-[#53615c] no-underline hover:bg-white hover:text-[#237b65]">Cancel</a>
    </div>

    <section class="max-w-3xl rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm sm:p-7">
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-md border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="m-0 font-semibold">Please correct the highlighted fields.</p>
            </div>
        @endif

        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="flex flex-col gap-5">
            @csrf
            @method('PUT')
            @include('suppliers._fields', ['supplier' => $supplier])

            <div class="flex flex-wrap justify-end gap-3 border-t border-[#edf1ef] pt-5">
                <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] no-underline hover:bg-[#f8faf9]">Cancel</a>
                <button type="submit" class="rounded-md border-0 bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]">Save changes</button>
            </div>
        </form>
    </section>
@endsection