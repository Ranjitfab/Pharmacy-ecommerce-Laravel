import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import CartDrawer from '../components/CartDrawer';
import ShopHeader from '../components/ShopHeader';

const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

export default function Checkout({ customerName, cart }) {
    const [processing, setProcessing] = useState(false);
    const [cartOpen, setCartOpen] = useState(false);
    const { errors = {} } = usePage().props;
    const canPlaceOrder = cart.items.every((item) => item.available && item.quantity <= item.stock);

    const placeOrder = () => {
        setProcessing(true);
        router.post('/checkout', {}, { onFinish: () => setProcessing(false) });
    };

    return (
        <>
            <Head title="Checkout" />
            <div className="min-h-screen bg-[#f7f9f6] text-[#25352e]">
                <ShopHeader customerName={customerName} cartCount={cart.itemCount} onOpenCart={() => setCartOpen(true)} />

                <main className="mx-auto max-w-5xl px-5 pb-16 pt-8 sm:px-8 sm:pt-12">
                    <Link href="/shop" className="text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                        Back to shop
                    </Link>
                    <header className="mb-8 mt-5 border-b border-[#e4e9e4] pb-6">
                        <p className="mb-2 mt-0 text-xs font-bold uppercase text-[#237b65]">RxStock · Order review</p>
                        <h1 className="m-0 text-3xl font-bold text-[#1d342d]">Checkout</h1>
                        <p className="mb-0 mt-2 text-sm text-[#718078]">Review your items before placing your order.</p>
                    </header>

                    {errors.cart && (
                        <p role="alert" className="mb-6 rounded-md border border-[#e6b7a8] bg-[#fff2ec] px-4 py-3 text-sm text-[#9a392b]">
                            {errors.cart}
                        </p>
                    )}

                    <div className="grid gap-8 lg:grid-cols-[1fr_20rem]">
                        <section aria-labelledby="checkout-items-heading">
                            <h2 id="checkout-items-heading" className="m-0 text-lg font-bold text-[#1d342d]">Order items</h2>
                            <ul className="m-0 mt-4 divide-y divide-[#e4e9e4] border-y border-[#e4e9e4] p-0">
                                {cart.items.map((item) => (
                                    <li key={item.id} className="flex flex-wrap items-center justify-between gap-3 py-4">
                                        <div>
                                            <h3 className="m-0 text-sm font-semibold text-[#25352e]">{item.name}</h3>
                                            <p className="mb-0 mt-1 text-sm text-[#718078]">
                                                {item.quantity} x {peso.format(Number(item.price))}
                                            </p>
                                            {!item.available || item.quantity > item.stock ? (
                                                <p className="mb-0 mt-1 text-xs font-semibold text-[#aa4c3c]">
                                                    Stock changed. Return to your cart to update this item.
                                                </p>
                                            ) : null}
                                        </div>
                                        <span className="text-sm font-bold tabular-nums text-[#1d342d]">
                                            {peso.format(Number(item.lineTotal))}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </section>

                        <aside className="h-fit border border-[#e4e9e4] bg-white p-5">
                            <h2 className="m-0 text-base font-bold text-[#1d342d]">Order summary</h2>
                            <div className="mt-5 flex items-center justify-between gap-3 border-b border-[#e9eeea] pb-4 text-sm text-[#59675f]">
                                <span>Subtotal</span>
                                <span className="font-semibold tabular-nums text-[#25352e]">
                                    {peso.format(Number(cart.subtotal))}
                                </span>
                            </div>
                            <p className="mb-0 mt-4 text-xs leading-5 text-[#718078]">
                                No payment is collected in this checkout. The order will be created with pending status.
                            </p>
                            <button
                                type="button"
                                onClick={placeOrder}
                                disabled={processing || !canPlaceOrder}
                                className="mt-5 w-full rounded-md bg-[#237b65] px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-not-allowed disabled:bg-[#dce4df] disabled:text-[#718078]"
                            >
                                {processing ? 'Placing order...' : 'Place order'}
                            </button>
                        </aside>
                    </div>
                </main>
                <CartDrawer
                    cart={cart}
                    errors={errors}
                    isOpen={cartOpen}
                    onClose={() => setCartOpen(false)}
                />
            </div>
        </>
    );
}