import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

const statuses = ['pending', 'shipped', 'delivered', 'cancelled'];
const statusStyles = {
    pending: 'bg-amber-100 text-amber-800',
    shipped: 'bg-sky-100 text-sky-800',
    delivered: 'bg-emerald-100 text-emerald-800',
    cancelled: 'bg-rose-100 text-rose-800',
};
const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

function OrderRow({ order, isExpanded, onToggle }) {
    const { data, setData, patch, processing, errors } = useForm({ status: order.status });

    const updateStatus = (event) => {
        event.preventDefault();
        patch(`/orders/${order.id}/status`, { preserveScroll: true });
    };

    return (
        <>
            <tr className="border-b border-[#edf0ed] transition-colors hover:bg-[#f8faf8]">
                <td className="px-4 py-4 align-middle">
                    <button
                        type="button"
                        onClick={onToggle}
                        aria-expanded={isExpanded}
                        aria-controls={`order-details-${order.id}`}
                        className="flex items-center gap-2 rounded-md text-sm font-semibold text-[#1d342d] hover:text-[#237b65] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                    >
                        <span aria-hidden="true" className="grid size-6 place-items-center rounded border border-[#dce4df] text-base leading-none">
                            {isExpanded ? '-' : '+'}
                        </span>
                        <span>#{order.id}</span>
                    </button>
                </td>
                <td className="px-4 py-4 text-sm text-[#43524b]">{order.customerName}</td>
                <td className="whitespace-nowrap px-4 py-4 text-sm tabular-nums text-[#59675f]">{order.date}</td>
                <td className="whitespace-nowrap px-4 py-4 text-sm text-[#59675f]">
                    {order.items.length} {order.items.length === 1 ? 'item' : 'items'}
                </td>
                <td className="whitespace-nowrap px-4 py-4 text-sm font-semibold tabular-nums text-[#25352e]">
                    {peso.format(Number(order.totalAmount))}
                </td>
                <td className="min-w-64 px-4 py-3">
                    <form onSubmit={updateStatus} className="flex items-start gap-2">
                        <div className="min-w-0 flex-1">
                            <label htmlFor={`status-${order.id}`} className="sr-only">Status for order {order.id}</label>
                            <select
                                id={`status-${order.id}`}
                                value={data.status}
                                onChange={(event) => setData('status', event.target.value)}
                                className={`w-full rounded-md border border-[#dce4df] px-2.5 py-2 text-xs font-semibold capitalize outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15 ${statusStyles[data.status] ?? 'bg-white text-[#43524b]'}`}
                            >
                                {statuses.map((status) => (
                                    <option key={status} value={status}>{status}</option>
                                ))}
                            </select>
                            {errors.status && <p role="alert" className="mb-0 mt-1 text-xs text-rose-700">{errors.status}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing || data.status === order.status}
                            className="rounded-md bg-[#237b65] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#1b6957] disabled:cursor-not-allowed disabled:bg-[#dce4df] disabled:text-[#718078]"
                        >
                            {processing ? 'Saving' : 'Save'}
                        </button>
                    </form>
                </td>
            </tr>
            {isExpanded && (
                <tr id={`order-details-${order.id}`} className="border-b border-[#e4e9e4] bg-[#f8faf8]">
                    <td colSpan="6" className="px-6 py-5">
                        <div className="max-w-3xl">
                            <h3 className="mb-3 mt-0 text-xs font-semibold uppercase text-[#718078]">Order details</h3>
                            <div className="overflow-x-auto">
                                <table className="w-full min-w-136 border-collapse text-left">
                                    <thead>
                                        <tr className="border-b border-[#e1e7e2] text-xs font-semibold uppercase text-[#718078]">
                                            <th className="py-2 pr-4">Product</th>
                                            <th className="py-2 pr-4">Qty</th>
                                            <th className="py-2 pr-4">Price at purchase</th>
                                            <th className="py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {order.items.map((item, index) => (
                                            <tr key={`${order.id}-${index}`} className="border-b border-[#e9eeea] last:border-0">
                                                <td className="py-3 pr-4 text-sm text-[#43524b]">{item.productName}</td>
                                                <td className="py-3 pr-4 text-sm tabular-nums text-[#59675f]">{item.quantity}</td>
                                                <td className="py-3 pr-4 text-sm tabular-nums text-[#59675f]">{peso.format(Number(item.price))}</td>
                                                <td className="py-3 text-right text-sm font-medium tabular-nums text-[#25352e]">
                                                    {peso.format(Number(item.subtotal))}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            )}
        </>
    );
}

export default function OrdersIndex({ adminName, orders, success }) {
    const [expandedOrders, setExpandedOrders] = useState(() => new Set());

    const toggleOrder = (orderId) => {
        setExpandedOrders((currentOrders) => {
            const nextOrders = new Set(currentOrders);
            if (nextOrders.has(orderId)) {
                nextOrders.delete(orderId);
            } else {
                nextOrders.add(orderId);
            }
            return nextOrders;
        });
    };

    return (
        <>
            <Head title="Manage orders" />
            <div className="min-h-screen bg-[#f4f7f6] text-[#26332f] md:flex">
                <aside className="hidden w-64 shrink-0 flex-col border-r border-[#e6ece9] bg-white md:flex">
                    <div className="border-b border-[#e6ece9] px-6 py-6">
                        <p className="m-0 text-xl font-bold text-[#237b65]">RxStock</p>
                        <p className="mb-0 mt-1 text-xs font-medium uppercase text-[#818c88]">Administration</p>
                    </div>
                    <nav aria-label="Admin navigation" className="flex flex-1 flex-col gap-2 p-4">
                        <Link href="/dashboard" className="rounded-md px-4 py-3 text-sm font-medium text-[#5c6663] transition-colors hover:bg-[#f4f7f6] hover:text-[#237b65]">Dashboard</Link>
                        <a href="/product-category/products" className="rounded-md px-4 py-3 text-sm font-medium text-[#5c6663] transition-colors hover:bg-[#f4f7f6] hover:text-[#237b65]">Manage products</a>
                        <a href="/product-category/product-categories" className="rounded-md px-4 py-3 text-sm font-medium text-[#5c6663] transition-colors hover:bg-[#f4f7f6] hover:text-[#237b65]">Product categories</a>
                        <a href="/suppliers" className="rounded-md px-4 py-3 text-sm font-medium text-[#5c6663] transition-colors hover:bg-[#f4f7f6] hover:text-[#237b65]">Suppliers</a>
                        <Link href="/orders" aria-current="page" className="rounded-md bg-[#e7f2ef] px-4 py-3 text-sm font-semibold text-[#237b65]">Manage orders</Link>
                    </nav>
                    <div className="border-t border-[#e6ece9] p-4">
                        <button
                            type="button"
                            onClick={() => router.post('/logout')}
                            className="w-full rounded-md px-4 py-3 text-left text-sm font-medium text-[#b94343] transition-colors hover:bg-red-50"
                        >
                            Log out
                        </button>
                    </div>
                </aside>

                <div className="min-w-0 flex-1">
                    <header className="border-b border-[#e6ece9] bg-white px-5 py-4 md:hidden">
                        <div className="mb-4 flex items-center justify-between gap-4">
                            <div>
                                <p className="m-0 text-lg font-bold text-[#237b65]">RxStock</p>
                                <p className="mb-0 mt-1 text-xs font-medium uppercase text-[#818c88]">Administration</p>
                            </div>
                            <button type="button" onClick={() => router.post('/logout')} className="px-3 py-2 text-sm font-medium text-[#b94343]">Log out</button>
                        </div>
                        <nav aria-label="Admin navigation" className="flex flex-wrap gap-2">
                            <Link href="/dashboard" className="rounded-md px-3 py-2 text-sm text-[#5c6663] hover:bg-[#f4f7f6]">Dashboard</Link>
                            <a href="/product-category/products" className="rounded-md px-3 py-2 text-sm text-[#5c6663] hover:bg-[#f4f7f6]">Manage products</a>
                            <a href="/product-category/product-categories" className="rounded-md px-3 py-2 text-sm text-[#5c6663] hover:bg-[#f4f7f6]">Product categories</a>
                            <a href="/suppliers" className="rounded-md px-3 py-2 text-sm text-[#5c6663] hover:bg-[#f4f7f6]">Suppliers</a>
                            <Link href="/orders" aria-current="page" className="rounded-md bg-[#e7f2ef] px-3 py-2 text-sm font-semibold text-[#237b65]">Orders</Link>
                        </nav>
                    </header>

                    <main className="mx-auto max-w-7xl p-5 sm:p-8">
                        <header className="mb-7">
                            <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Administration</p>
                            <h1 className="m-0 text-2xl font-bold text-[#26332f]">Manage orders</h1>
                            <p className="mb-0 mt-2 text-sm text-[#71807b]">Customer orders, newest first.</p>
                        </header>

                        {success && (
                            <p role="status" className="mb-5 rounded-md border border-[#cde2da] bg-[#e7f2ef] px-4 py-3 text-sm font-medium text-[#237b65]">
                                {success}
                            </p>
                        )}

                        <section aria-label="Orders" className="overflow-hidden border border-[#e6ece9] bg-white shadow-sm">
                            {orders.data.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <table className="w-full min-w-248 border-collapse text-left">
                                        <thead>
                                            <tr className="border-b border-[#e6ece9] bg-[#f8faf8]">
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Order ID</th>
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Customer</th>
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Date</th>
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Items</th>
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Total</th>
                                                <th className="px-4 py-3 text-xs font-semibold uppercase text-[#718078]">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {orders.data.map((order) => (
                                                <OrderRow
                                                    key={order.id}
                                                    order={order}
                                                    isExpanded={expandedOrders.has(order.id)}
                                                    onToggle={() => toggleOrder(order.id)}
                                                />
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <p className="m-0 px-5 py-10 text-sm text-[#718078]">No orders have been placed yet.</p>
                            )}
                        </section>

                        {orders.links.length > 3 && (
                            <nav aria-label="Order pages" className="mt-6 flex flex-wrap justify-center gap-2">
                                {orders.links.slice(1, -1).map((link) => link.url ? (
                                    <Link
                                        key={link.label}
                                        href={link.url}
                                        aria-current={link.active ? 'page' : undefined}
                                        className={`grid min-h-10 min-w-10 place-items-center rounded-md border px-3 text-sm font-semibold no-underline ${link.active ? 'border-[#237b65] bg-[#237b65] text-white' : 'border-[#dce4df] bg-white text-[#43524b] hover:bg-[#f4f7f6]'}`}
                                    >
                                        {link.label.replace('&laquo;', '‹').replace('&raquo;', '›')}
                                    </Link>
                                ) : null)}
                            </nav>
                        )}
                    </main>
                </div>
            </div>
        </>
    );
}