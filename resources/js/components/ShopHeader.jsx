import { Link, router } from '@inertiajs/react';

export default function ShopHeader({ customerName, cartCount, wishlistCount, onOpenCart }) {
    return (
        <header className="border-b border-[#e4e9e4] bg-white">
            <div className="mx-auto flex min-h-18 max-w-7xl items-center justify-between gap-4 px-5 sm:px-8">
                <div className="flex items-center gap-8">
                    <Link href="/shop" className="flex items-center gap-2 text-xl font-bold text-[#1d342d] no-underline">
                        <span className="grid size-9 place-items-center rounded-md bg-[#237b65] text-sm font-bold text-white">Rx</span>
                        <span>RxStock</span>
                    </Link>
                    <nav className="hidden sm:block" aria-label="Main navigation">
                        <Link
                            href="/shop"
                            aria-current="page"
                            className="border-b-2 border-[#237b65] px-1 py-6 text-sm font-semibold text-[#237b65] no-underline"
                        >
                            Shop
                        </Link>
                    </nav>
                </div>

                <div className="flex items-center gap-3 sm:gap-5">
                    <span className="hidden text-sm text-[#69766f] md:inline">Hi, {customerName}</span>
                    <Link
                        href="/wishlist"
                        className="whitespace-nowrap text-sm font-semibold text-[#43524b] no-underline hover:text-[#237b65]"
                    >
                        Wishlist ({wishlistCount})
                    </Link>
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
                        className="rounded-md border border-[#dce4df] bg-white px-3 py-2 text-sm font-semibold text-[#43524b] transition-colors hover:border-[#b9cec3] hover:bg-[#f7faf8] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                    >
                        Log out
                    </button>
                </div>
            </div>
        </header>
    );
}