@extends('layouts.app')

@section('content')

    <h1>{{$product->name}}</h1>

    <p>
        <strong>Category:</strong>
        {{$product->category->name}}
    </p>

    <p>
        <strong>Price:</strong>
        ₱{{product->price}}
    </p>

    <p>
        <strong>Stock:</strong>
        {{$product->stock}}
    </p>

    <p>
        <strong>Expiration Date:</strong>
        {{$product->expiration_date}}
    </p>

    <p>{{$product->description}}</p>
    



    <a href="{{route('product.index')}}">
        Back
    </a>
    
@endsection