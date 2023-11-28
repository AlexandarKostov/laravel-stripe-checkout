<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>
<body class="antialiased">
   <div style="display: flex; gap: 2rem;">
       @foreach($products as $product)
           <div style="flex: 1">
               <h4>Product details</h4>
               <img src="{{ $product->image }}" alt="" style="max-width: 100%;">
               <h5>{{ $product->name }}</h5>
               <p>$ {{ $product->price }}</p>
           </div>
       @endforeach
   </div>
        <div>
            <form action="{{ route('checkout') }}" method="post">
                @csrf
                @method('POST')
                <button>Checkout</button>
            </form>
        </div>
</body>
</html>
