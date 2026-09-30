<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trim($__env->yieldContent('title', 'RxStock Admin')) }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#f4f7f6] font-sans text-[#26332f] antialiased md:flex">
    <x-admin-sidebar />

    <div class="min-w-0 flex-1">
        <main class="mx-auto w-full max-w-7xl px-5 py-8 sm:px-8 sm:py-10">
            @if (session('success'))
                <div role="status" class="mb-6 rounded-md border-l-4 border-[#237b65] bg-white px-4 py-3 text-sm text-[#275d4e] shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="mb-6 rounded-md border-l-4 border-red-500 bg-white px-4 py-3 text-sm text-red-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>