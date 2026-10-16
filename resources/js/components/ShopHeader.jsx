import { Link, router, usePage } from '@inertiajs/react';

export default function ShopHeader({ customerName, cartCount, onOpenCart }) {
    const currentPath = usePage().url.split('?')[0];

    return (
        <header className="border-b border-[#e4e9e4] bg-white">
            <div className="mx-auto flex min-h-18 max-w-7xl flex-wrap items-center gap-x-4 gap-y-2 px-5 py-3 sm:flex-nowrap sm:px-8">
                <div className="flex items-center gap-5 sm:gap-8">
                    <Link href="/shop" className="flex items-center gap-2 text-xl font-bold text-[#1d342d] no-underline">
                        <span className="grid size-9 place-items-center rounded-md bg-[#237b65] text-sm font-bold text-white">Rx</span>
                        <span>RxStock</span>
                    </Link>
                    <nav className="order-3 flex w-full items-center gap-4 sm:order-none sm:w-auto" aria-label="Main navigation">
                        <Link
                            href="/shop"
                            aria-current={currentPath === '/shop' ? 'page' : undefined}
                            className={`border-b-2 px-1 py-2 text-sm no-underline ${currentPath === '/shop' ? 'border-[#237b65] font-semibold text-[#237b65]' : 'border-transparent font-medium text-[#59675f] hover:text-[#237b65]'}`}
                        >
                            Shop
                        </Link>
                        <Link
                            href="/my-orders"
                            aria-current={currentPath === '/my-orders' ? 'page' : undefined}
                            className={`border-b-2 px-1 py-2 text-sm no-underline ${currentPath === '/my-orders' ? 'border-[#237b65] font-semibold text-[#237b65]' : 'border-transparent font-medium text-[#59675f] hover:text-[#237b65]'}`}
                        >
                            My orders
                        </Link>
                    </nav>
                </div>

                <div className="ml-auto flex items-center gap-2 sm:gap-5">
                    <span className="hidden text-sm text-[#69766f] md:inline">Hi, {customerName}</span>
                    {onOpenCart && (
                        <button
                            type="button"
                            onClick={onOpenCart}
                            aria-label={`Open cart, ${cartCount} ${cartCount === 1 ? 'item' : 'items'}`}
                            className="flex min-h-10 items-center gap-2 rounded-md border border-[#dce4df] bg-white px-3 py-2 text-sm font-semibold text-[#43524b] transition-colors hover:border-[#b9cec3] hover:bg-[#f7faf8] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                        >
                            <span>Cart</span>
                            <span className="grid min-w-6 place-items-center rounded-full bg-[#1d342d] px-1.5 py-0.5 text-xs tabular-nums text-white">
                                {cartCount}
                            </span>
                        </button>
                    )}
                    <button
                        type="button"
                        onClick={() => router.post('/logout')}
                        className="rounded-md border border-[#f0d6d2] bg-white px-3 py-2 text-sm font-semibold text-[#b94343] transition-colors hover:border-[#e6b7a8] hover:bg-[#fff2ec] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#b94343]"
                    >
                        Log out
                    </button>
                </div>
            </div>
        </header>
    );
}