import { Head, Link, router, usePage } from '@inertiajs/react';

const alertStyles = {
    expired: 'border-red-500 bg-red-50 text-red-900',
    expiring_soon: 'border-amber-500 bg-amber-50 text-amber-900',
    low_stock: 'border-orange-500 bg-orange-50 text-orange-900',
};

export default function Dashboard({ adminName, stats, alerts }) {
    const { url, props: { auth } } = usePage();
    const currentPath = url.split('?')[0];
    const roleLabel = auth?.user?.roles?.includes('admin') ? 'Admin' : 'Manager';
    const logout = () => router.post('/logout');

    const navigationItems = [
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Products', href: '/product-category/products' },
        { label: 'Product categories', href: '/product-category/product-categories' },
        { label: 'Suppliers', href: '/suppliers' },
        { label: 'Orders', href: '/orders' },
    ];

    const navigation = (
        <nav className="flex flex-wrap gap-2 md:flex-col">
            {navigationItems.map((item) => {
                const isActive = currentPath === item.href || currentPath.startsWith(`${item.href}/`);

                return (
                    <Link
                        key={item.href}
                        href={item.href}
                        aria-current={isActive ? 'page' : undefined}
                        className={`rounded-md px-4 py-3 text-sm transition-colors ${
                            isActive
                                ? 'bg-[#e7f2ef] font-semibold text-[#237b65]'
                                : 'font-medium text-[#5c6663] hover:bg-[#f4f7f6] hover:text-[#237b65]'
                        }`}
                    >
                        {item.label}
                    </Link>
                );
            })}
        </nav>
    );

    return (
        <>
            <Head title={`${roleLabel} dashboard`} />

            <div className="min-h-screen bg-[#f4f7f6] text-[#26332f] md:flex">
                <aside className="hidden w-64 shrink-0 flex-col border-r border-[#e6ece9] bg-white md:flex">
                    <div className="border-b border-[#e6ece9] px-6 py-6">
                        <p className="m-0 text-xl font-bold tracking-normal text-[#237b65]">
                            RxStock
                        </p>
                        <p className="mb-0 mt-1 text-xs font-medium uppercase text-[#818c88]">
                            Administration
                        </p>
                    </div>

                    <div className="flex-1 p-4">{navigation}</div>

                    <div className="border-t border-[#e6ece9] p-4">
                        <button
                            type="button"
                            onClick={logout}
                            className="w-full rounded-md px-4 py-3 text-left text-sm font-medium text-[#b94343] transition-colors hover:bg-red-50"
                        >
                            Log out
                        </button>
                    </div>
                </aside>

                <div className="min-w-0 flex-1">
                    <header className="border-b border-[#e6ece9] bg-white px-5 py-4 md:hidden">
                        <div className="mb-4 flex items-center justify-between">
                            <div>
                                <p className="m-0 text-lg font-bold text-[#237b65]">RxStock</p>
                                <p className="mb-0 mt-0.5 text-xs text-[#818c88]">Administration</p>
                            </div>
                            <button
                                type="button"
                                onClick={logout}
                                className="rounded-md px-3 py-2 text-sm font-medium text-[#b94343] hover:bg-red-50"
                            >
                                Log out
                            </button>
                        </div>
                        {navigation}
                    </header>

                    <main className="mx-auto w-full max-w-7xl p-5 sm:p-8">
                        <header className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                                    {roleLabel} overview
                                </p>
                                <h1 className="m-0 text-2xl font-bold text-[#26332f]">
                                    Welcome, {adminName}
                                </h1>
                                <p className="mb-0 mt-2 text-sm text-[#71807b]">
                                    Inventory and order activity at a glance.
                                </p>
                            </div>
                            <span className="w-fit rounded-full border border-[#cde2da] bg-white px-3 py-1.5 text-xs font-semibold text-[#237b65]">
                                {roleLabel === 'Admin' ? 'Administrator' : 'Manager'}
                            </span>
                        </header>

                        <section aria-label="Store totals" className="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            <article className="rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm">
                                <h2 className="m-0 text-xs font-semibold uppercase text-[#71807b]">
                                    Total products
                                </h2>
                                <p className="mb-0 mt-3 text-3xl font-bold tabular-nums text-[#26332f]">
                                    {stats.products}
                                </p>
                            </article>
                            <article className="rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm">
                                <h2 className="m-0 text-xs font-semibold uppercase text-[#71807b]">
                                    Total orders
                                </h2>
                                <p className="mb-0 mt-3 text-3xl font-bold tabular-nums text-[#26332f]">
                                    {stats.orders}
                                </p>
                            </article>
                            <article className="rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
                                <h2 className="m-0 text-xs font-semibold uppercase text-[#71807b]">
                                    Total customers
                                </h2>
                                <p className="mb-0 mt-3 text-3xl font-bold tabular-nums text-[#26332f]">
                                    {stats.customers}
                                </p>
                            </article>
                        </section>

                        <section className="mb-8" aria-labelledby="alerts-heading">
                            <div className="mb-4 flex items-center justify-between gap-4">
                                <h2 id="alerts-heading" className="m-0 text-lg font-bold text-[#26332f]">
                                    {roleLabel} alerts
                                </h2>
                                <span className="rounded-full bg-white px-2.5 py-1 text-xs font-semibold tabular-nums text-[#71807b]">
                                    {alerts.length}
                                </span>
                            </div>

                            {alerts.length > 0 ? (
                                <ul className="m-0 grid list-none gap-3 p-0 sm:grid-cols-2">
                                    {alerts.map((alert, index) => (
                                        <li
                                            key={`${alert.type}-${index}`}
                                            className={`rounded-md border-l-4 p-4 text-sm ${alertStyles[alert.type] ?? 'border-gray-400 bg-gray-50 text-gray-800'}`}
                                        >
                                            <p className="m-0 font-semibold">{alert.title}</p>
                                            <p className="mb-0 mt-1 leading-5">{alert.message}</p>
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="m-0 rounded-lg border border-[#e6ece9] bg-white p-5 text-sm text-[#71807b]">
                                    No inventory alerts right now.
                                </p>
                            )}
                        </section>

                        <section aria-labelledby="quick-links-heading">
                            <h2 id="quick-links-heading" className="mb-4 mt-0 text-lg font-bold text-[#26332f]">
                                Quick links
                            </h2>
                            <div className="flex flex-wrap gap-3">
                                <a
                                    href="/product-category/products"
                                    className="rounded-md border border-[#dce5e1] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] transition-colors hover:border-[#b8d3c9] hover:bg-[#f8fbf9]"
                                >
                                    Manage products
                                </a>
                                <a
                                    href="/product-category/products/create"
                                    className="rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957]"
                                >
                                    Add product
                                </a>
                                <Link
                                    href="/orders"
                                    className="rounded-md border border-[#dce5e1] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] transition-colors hover:border-[#b8d3c9] hover:bg-[#f8fbf9]"
                                >
                                    Manage orders
                                </Link>
                            </div>
                        </section>
                    </main>
                </div>
            </div>
        </>
    );
}