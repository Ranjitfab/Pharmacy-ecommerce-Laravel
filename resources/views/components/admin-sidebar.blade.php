<aside class="flex w-full shrink-0 flex-col border-b border-[#e6ece9] bg-white md:sticky md:top-0 md:h-screen md:w-64 md:border-b-0 md:border-r">
    <div class="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4 md:block md:px-6 md:py-6">
        <div>
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-[#237b65] no-underline">
                RxStock
            </a>
            <p class="mb-0 mt-1 text-xs font-medium uppercase text-[#818c88]">
                Administration
            </p>
        </div>

        @auth
            <form action="{{ route('logout') }}" method="POST" class="md:hidden">
                @csrf
                <button type="submit" class="rounded-md px-3 py-2 text-sm font-medium text-[#b94343] transition-colors hover:bg-red-50">
                    Log out
                </button>
            </form>
        @endauth
    </div>

    <nav aria-label="Admin navigation" class="flex flex-wrap gap-2 overflow-x-auto p-3 md:flex-1 md:flex-col md:flex-nowrap md:overflow-visible md:p-4">
        <a
            href="{{ route('dashboard') }}"
            @if (request()->routeIs('dashboard')) aria-current="page" @endif
            class="rounded-md px-4 py-3 text-sm font-medium no-underline transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#e7f2ef] font-semibold text-[#237b65]' : 'text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]' }}"
        >
            Dashboard
        </a>
        <a
            href="{{ route('products.index') }}"
            @if (request()->routeIs('products.*')) aria-current="page" @endif
            class="rounded-md px-4 py-3 text-sm font-medium no-underline transition-colors {{ request()->routeIs('products.*') ? 'bg-[#e7f2ef] font-semibold text-[#237b65]' : 'text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]' }}"
        >
            Manage products
        </a>
        <a
            href="{{ route('product-categories.index') }}"
            @if (request()->routeIs('product-categories.*')) aria-current="page" @endif
            class="rounded-md px-4 py-3 text-sm font-medium no-underline transition-colors {{ request()->routeIs('product-categories.*') ? 'bg-[#e7f2ef] font-semibold text-[#237b65]' : 'text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]' }}"
        >
            Product categories
        </a>
        <a
            href="{{ route('suppliers.index') }}"
            @if (request()->routeIs('suppliers.*')) aria-current="page" @endif
            class="rounded-md px-4 py-3 text-sm font-medium no-underline transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-[#e7f2ef] font-semibold text-[#237b65]' : 'text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]' }}"
        >
            Suppliers
        </a>
        <a
            href="{{ route('orders.index') }}"
            @if (request()->routeIs('orders.*')) aria-current="page" @endif
            class="rounded-md px-4 py-3 text-sm font-medium no-underline transition-colors {{ request()->routeIs('orders.*') ? 'bg-[#e7f2ef] font-semibold text-[#237b65]' : 'text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]' }}"
        >
            Manage orders
        </a>
    </nav>

    <div class="hidden border-t border-[#e6ece9] p-4 md:block">
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full rounded-md px-4 py-3 text-left text-sm font-medium text-[#b94343] transition-colors hover:bg-red-50">
                    Log out
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block rounded-md px-4 py-3 text-sm font-medium text-[#5c6663] no-underline transition-colors hover:bg-[#f4f7f6] hover:text-[#237b65]">
                Log in
            </a>
        @endauth
    </div>
</aside>