import { Head, Link, router, usePage } from '@inertiajs/react';

const navigation = [
    { label: 'Dashboard', href: '/dashboard', roles: ['admin', 'manager'] },
    { label: 'Products', href: '/product-category/products', permission: 'view products' },
    { label: 'Product categories', href: '/product-category/product-categories', roles: ['admin', 'manager'] },
    { label: 'Suppliers', href: '/suppliers', roles: ['admin', 'manager'] },
    { label: 'Orders', href: '/orders', roles: ['admin', 'manager'] },
];

export default function AdminLayout({ title, success, children }) {
    const { url, props: { auth } } = usePage();
    const currentPath = url.split('?')[0];
    const permissions = auth?.user?.permissions ?? [];
    const roles = auth?.user?.roles ?? [];
    const logout = () => router.post('/logout');

    const links = (className = '') => (
        <nav className={`flex flex-wrap gap-2 md:flex-col ${className}`}>
            {navigation
                .filter((item) => {
                    if (item.permission) {
                        return permissions.includes(item.permission);
                    }

                    return item.roles.some((role) => roles.includes(role));
                })
                .map((item) => {
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
            <Head title={`${title} | RxStock`} />

            <div className="min-h-screen bg-[#f4f7f6] text-[#26332f] md:flex">
                <aside className="hidden w-64 shrink-0 flex-col border-r border-[#e6ece9] bg-white md:flex">
                    <div className="border-b border-[#e6ece9] px-6 py-6">
                        <p className="m-0 text-xl font-bold text-[#237b65]">RxStock</p>
                        <p className="mb-0 mt-1 text-xs font-medium uppercase text-[#818c88]">Administration</p>
                    </div>
                    <div className="flex-1 p-4">{links()}</div>
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
                        {links()}
                    </header>

                    <main className="mx-auto w-full max-w-7xl p-5 sm:p-8">
                        {success && (
                            <p role="status" className="mb-6 rounded-md border-l-4 border-[#237b65] bg-white px-4 py-3 text-sm text-[#275d4e] shadow-sm">
                                {success}
                            </p>
                        )}
                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}
