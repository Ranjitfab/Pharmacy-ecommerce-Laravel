@extends('layouts.app')

@section('content')

    <h1>Edit Product</h1>
    <form action="{{ route('products.update', $product) }}" 
            method="POST">
        @csrf
        @method('PUT')

        <input 
            type="text"
            name="name"
            value="{{$product->name}}"
        >

        <select name="product_category_id">
            @foreach($categories as $category)
                <option 
                    value="{{$category->id}}"
                    @selected($product->procuct_category_id == $category->id)
                >
                    {{$category->name}}
                </option>
            @endforeach
        </select>

        <input type="type" name="price" value="{{$product->price}}">

        <input type="number" name="stock" value="{{$product->stock}}">

        <input type="date" name="expiration_date" value="{{$product->expiration_date}}">

        <textarea name="description">{{$product->description}}</textarea>

        <button type="submit">Update</button>
    </form>
    
@endsection