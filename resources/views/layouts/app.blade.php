<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rx Hub</title>
</head>
<body>

    <nav>
        <a href="{{ route('products.index')}}">Products</a>
        <a href="{{ route('product-categories.index')}}">Categories</a>    
    </nav>
    
    <hr>

    @if(session('success'))
        <p style="color: green;">
            {{session('success')}}
        </p>
    @endif

    @yield('content')
</body>
</html>