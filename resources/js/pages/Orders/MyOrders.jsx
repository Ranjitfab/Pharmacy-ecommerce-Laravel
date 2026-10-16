import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import CartDrawer from '../../components/CartDrawer';
import ShopHeader from '../../components/ShopHeader';

const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
const statusStyles = {
    pending: 'bg-amber-100 text-amber-900',
    shipped: 'bg-sky-100 text-sky-900',
    delivered: 'bg-emerald-100 text-emerald-900',
    cancelled: 'bg-rose-100 text-rose-900',
};

export default function MyOrders({ customerName, cart, orders }) {
    const [cartOpen, setCartOpen] = useState(false);
    const { errors = {} } = usePage().props;

    return (
        <>
            <Head title="My orders" />
            <div className="min-h-screen bg-[#f7f9f6] text-[#25352e]">
                <ShopHeader
                    customerName={customerName}
                    cartCount={cart.itemCount}
                    onOpenCart={() => setCartOpen(true)}
                />

                <main className="mx-auto max-w-5xl px-5 pb-16 pt-8 sm:px-8 sm:pt-12">
                    <header className="mb-8 flex flex-col gap-3 border-b border-[#e4e9e4] pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p className="mb-2 mt-0 text-xs font-bold uppercase text-[#237b65]">RxStock · Account</p>
                            <h1 className="m-0 text-3xl font-bold text-[#1d342d]">My orders</h1>
                            <p className="mb-0 mt-2 text-sm text-[#718078]">View your order history and item details.</p>
                        </div>
                        <Link href="/shop" className="w-fit text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                            Continue shopping
                        </Link>
                    </header>

                    {orders.data.length > 0 ? (
                        <ol className="m-0 flex list-none flex-col gap-4 p-0">
                            {orders.data.map((order) => (
                                <li key={order.id}>
                                    <article className="overflow-hidden rounded-lg border border-[#e4e9e4] bg-white">
                                        <header className="flex flex-col gap-3 border-b border-[#e9eeea] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h2 className="m-0 text-base font-bold text-[#25352e]">Order #{order.id}</h2>
                                                <p className="mb-0 mt-1 text-sm text-[#718078]">Placed {order.date}</p>
                                            </div>
                                            <div className="flex items-center justify-between gap-4 sm:justify-end">
                                                <span className={`rounded-full px-3 py-1 text-xs font-semibold capitalize ${statusStyles[order.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                    {order.status}
                                                </span>
                                                <span className="text-sm font-bold tabular-nums text-[#25352e]">
                                                    {peso.format(Number(order.total))}
                                                </span>
                                            </div>
                                        </header>

                                        <ul className="m-0 divide-y divide-[#edf1ef] px-5 py-1">
                                            {order.items.map((item, index) => (
                                                <li key={`${order.id}-${index}`} className="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                                                    <span className="font-medium text-[#35443e]">
                                                        {item.productName} <span className="text-[#718078]">× {item.quantity}</span>
                                                    </span>
                                                    <span className="tabular-nums text-[#59675f]">
                                                        {peso.format(Number(item.unitPrice))} each
                                                    </span>
                                                </li>
                                            ))}
                                        </ul>
                                    </article>
                                </li>
                            ))}
                        </ol>
                    ) : (
                        <section className="border-y border-[#e4e9e4] py-16 text-center">
                            <h2 className="m-0 text-lg font-semibold text-[#25352e]">No orders yet</h2>
                            <p className="mb-0 mt-2 text-sm text-[#718078]">Orders you place will appear here.</p>
                            <Link href="/shop" className="mt-4 inline-block text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                                Browse the shop
                            </Link>
                        </section>
                    )}

                    {orders.links.length > 3 && (
                        <nav aria-label="Order pages" className="mt-6 flex flex-wrap justify-center gap-2">
                            {orders.links.slice(1, -1).map((link) => link.url ? (
                                <Link
                                    key={link.label}
                                    href={link.url}
                                    aria-current={link.active ? 'page' : undefined}
                                    className={`grid min-h-10 min-w-10 place-items-center rounded-md border px-3 text-sm font-semibold no-underline ${link.active ? 'border-[#237b65] bg-[#237b65] text-white' : 'border-[#dce4df] bg-white text-[#43524b] hover:bg-[#edf3ef]'}`}
                                >
                                    {link.label.replace('&laquo;', '‹').replace('&raquo;', '›')}
                                </Link>
                            ) : null)}
                        </nav>
                    )}
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