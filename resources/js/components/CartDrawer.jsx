import { Link, router } from '@inertiajs/react';

const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

export default function CartDrawer({ cart, errors, isOpen, onClose }) {
    if (!isOpen) {
        return null;
    }

    const updateQuantity = (item, quantity) => {
        router.patch(`/cart/items/${item.id}`, { quantity }, { preserveScroll: true });
    };

    const removeItem = (item) => {
        router.delete(`/cart/items/${item.id}`, { preserveScroll: true });
    };

    const checkoutBlocked = cart.items.some((item) => !item.available || item.quantity > item.stock);

    return (
        <>
            <button
                type="button"
                aria-label="Close cart"
                onClick={onClose}
                className="fixed inset-0 z-40 cursor-default bg-[#16241e]/40"
            />
            <aside
                role="dialog"
                aria-modal="true"
                aria-labelledby="cart-title"
                className="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col border-l border-[#e4e9e4] bg-white shadow-2xl"
            >
                <header className="flex items-center justify-between border-b border-[#e4e9e4] px-5 py-5">
                    <div>
                        <h2 id="cart-title" className="m-0 text-lg font-bold text-[#1d342d]">Your cart</h2>
                        <p className="mb-0 mt-1 text-sm text-[#718078]">
                            {cart.itemCount} {cart.itemCount === 1 ? 'item' : 'items'}
                        </p>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-md px-3 py-2 text-sm font-semibold text-[#59675f] hover:bg-[#f2f5f2] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                    >
                        Close
                    </button>
                </header>

                {errors.cart && (
                    <p role="alert" className="mx-5 mt-4 rounded-md bg-[#fff2ec] px-3 py-2.5 text-sm text-[#9a392b]">
                        {errors.cart}
                    </p>
                )}
                {errors.quantity && (
                    <p role="alert" className="mx-5 mt-4 rounded-md bg-[#fff2ec] px-3 py-2.5 text-sm text-[#9a392b]">
                        {errors.quantity}
                    </p>
                )}

                {cart.items.length > 0 ? (
                    <>
                        <ul className="m-0 flex-1 divide-y divide-[#e9eeea] overflow-y-auto px-5 py-2">
                            {cart.items.map((item) => (
                                <li key={item.id} className="py-5">
                                    <div className="flex items-start justify-between gap-4">
                                        <div className="min-w-0">
                                            <h3 className="m-0 line-clamp-2 text-sm font-semibold text-[#25352e]">{item.name}</h3>
                                            <p className="mb-0 mt-1 text-sm text-[#718078]">{peso.format(Number(item.price))} each</p>
                                            {!item.available && (
                                                <p className="mb-0 mt-2 text-xs font-semibold text-[#aa4c3c]">Currently unavailable</p>
                                            )}
                                            {item.available && item.quantity > item.stock && (
                                                <p className="mb-0 mt-2 text-xs font-semibold text-[#aa4c3c]">
                                                    Only {item.stock} available; reduce quantity to continue.
                                                </p>
                                            )}
                                        </div>
                                        <p className="m-0 shrink-0 text-sm font-bold tabular-nums text-[#1d342d]">
                                            {peso.format(Number(item.lineTotal))}
                                        </p>
                                    </div>
                                    <div className="mt-4 flex items-center justify-between">
                                        {item.available ? (
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    aria-label={`Decrease ${item.name} quantity`}
                                                    onClick={() => updateQuantity(item, item.quantity - 1)}
                                                    disabled={item.quantity <= 1}
                                                    className="grid size-9 place-items-center rounded-md border border-[#dce4df] text-lg text-[#43524b] hover:bg-[#f2f5f2] disabled:cursor-not-allowed disabled:opacity-40"
                                                >
                                                    -
                                                </button>
                                                <output className="min-w-7 text-center text-sm font-semibold tabular-nums text-[#25352e]">
                                                    {item.quantity}
                                                </output>
                                                <button
                                                    type="button"
                                                    aria-label={`Increase ${item.name} quantity`}
                                                    onClick={() => updateQuantity(item, item.quantity + 1)}
                                                    disabled={item.quantity >= item.stock}
                                                    className="grid size-9 place-items-center rounded-md border border-[#dce4df] text-lg text-[#43524b] hover:bg-[#f2f5f2] disabled:cursor-not-allowed disabled:opacity-40"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        ) : <span className="text-xs text-[#718078]">Remove item to continue</span>}
                                        <button
                                            type="button"
                                            onClick={() => removeItem(item)}
                                            className="px-2 py-2 text-xs font-semibold text-[#a34235] underline-offset-4 hover:underline"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </li>
                            ))}
                        </ul>

                        <footer className="border-t border-[#e4e9e4] px-5 py-5">
                            <div className="mb-4 flex items-center justify-between gap-4">
                                <span className="text-sm font-medium text-[#59675f]">Subtotal</span>
                                <span className="text-lg font-bold tabular-nums text-[#1d342d]">
                                    {peso.format(Number(cart.subtotal))}
                                </span>
                            </div>
                            {checkoutBlocked ? (
                                <button
                                    type="button"
                                    disabled
                                    className="w-full cursor-not-allowed rounded-md bg-[#dce4df] px-4 py-3 text-center text-sm font-semibold text-[#718078]"
                                >
                                    Update cart to continue
                                </button>
                            ) : (
                                <Link
                                    href="/checkout"
                                    onClick={onClose}
                                    className="block rounded-md bg-[#237b65] px-4 py-3 text-center text-sm font-semibold text-white no-underline hover:bg-[#1b6957]"
                                >
                                    Continue to checkout
                                </Link>
                            )}
                        </footer>
                    </>
                ) : (
                    <div className="flex flex-1 flex-col items-center justify-center px-8 text-center">
                        <p className="m-0 text-base font-semibold text-[#25352e]">Your cart is empty</p>
                        <p className="mb-0 mt-2 text-sm text-[#718078]">Add an in-stock product to get started.</p>
                        <button
                            type="button"
                            onClick={onClose}
                            className="mt-5 rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957]"
                        >
                            Continue shopping
                        </button>
                    </div>
                )}
            </aside>
        </>
    );
}