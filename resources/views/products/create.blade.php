@extends('layouts.app')

@section('content')

    <h1>Create Product</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <input type="text" name="name" placeholder="Product Name" value="{{ old('name') }}" required>

        <select name="product_category_id" required>

            <option value="">Select Category</option>
            @forelse ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('product_category_id') == $category->id)>
                    {{$category->name}}
                </option>
            @empty
                <option value="" disabled>
                    No categories available
                </option>
            @endforelse

        </select>

        <input type="number" name="price" placeholder="Price" step="0.01" min="0" value="{{ old('price') }}" required>

        <input type="number" name="stock" placeholder="Stock" min="0" value="{{ old('stock', 0) }}" required>

        <textarea name="description" placeholder="Description">{{ old('description') }}</textarea>

        <input type="date" name="expiration_date" value="{{ old('expiration_date') }}">

        <button type="submit">Save</button>
    </form>
    
@endsection