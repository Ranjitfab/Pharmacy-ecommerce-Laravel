import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import CartDrawer from '../components/CartDrawer';
import ProductCard from '../components/ProductCard';
import ShopHeader from '../components/ShopHeader';

export default function Shop({ customerName, products, categories, filters, cart, success }) {
    const [cartOpen, setCartOpen] = useState(false);
    const [addingProductId, setAddingProductId] = useState(null);
    const { errors = {} } = usePage().props;
    const { data, setData } = useForm({
        search: filters.search ?? '',
        category: filters.category ? String(filters.category) : '',
        sort: filters.sort ?? 'newest',
    });

    const visitShop = (nextFilters) => {
        router.get('/shop', nextFilters, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    const searchProducts = (event) => {
        event.preventDefault();
        visitShop({ ...data, search: data.search.trim(), page: 1 });
    };

    const selectCategory = (category) => {
        const value = category ? String(category.id) : '';
        setData('category', value);
        visitShop({ ...data, category: value, page: 1 });
    };

    const selectSort = (event) => {
        const value = event.target.value;
        setData('sort', value);
        visitShop({ ...data, sort: value, page: 1 });
    };

    const addToCart = (product) => {
        setAddingProductId(product.id);
        router.post('/cart/items', { product_id: product.id, quantity: 1 }, {
            preserveScroll: true,
            onSuccess: () => setCartOpen(true),
            onError: () => setCartOpen(true),
            onFinish: () => setAddingProductId(null),
        });
    };

    return (
        <>
            <Head title="Shop" />
            <div className="min-h-screen bg-[#f7f9f6] text-[#25352e]">
                <ShopHeader
                    customerName={customerName}
                    cartCount={cart.itemCount}
                    onOpenCart={() => setCartOpen(true)}
                />

                <main className="mx-auto max-w-7xl px-5 pb-16 pt-8 sm:px-8 sm:pt-12">
                    {success && (
                        <p role="status" className="mb-6 rounded-md border border-[#b8d9ca] bg-[#e9f5ee] px-4 py-3 text-sm font-medium text-[#245c43]">
                            {success}
                        </p>
                    )}
                    <section className="mb-9 grid items-center gap-6 border-b border-[#e4e9e4] pb-9 md:grid-cols-[1fr_0.75fr] md:gap-10 md:pb-11">
                        <div>
                            <p className="mb-3 mt-0 text-xs font-bold uppercase text-[#237b65]">RxStock · Customer shop</p>
                            <h1 className="m-0 max-w-xl text-3xl font-bold leading-tight text-[#1d342d] sm:text-4xl">
                                Everyday care, made easier.
                            </h1>
                            <p className="mb-0 mt-3 max-w-lg text-sm leading-6 text-[#69766f]">
                                Browse pharmacy essentials selected for your daily routine.
                            </p>
                        </div>
                    </section>

                    <section aria-labelledby="catalog-heading">
                        <div className="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <h2 id="catalog-heading" className="m-0 text-xl font-bold text-[#1d342d]">
                                    Shop all products
                                </h2>
                                <p className="mb-0 mt-1 text-sm text-[#718078]">
                                    {products.total} {products.total === 1 ? 'product' : 'products'} listed
                                </p>
                            </div>

                            <div className="flex flex-col gap-3 sm:flex-row">
                                <form onSubmit={searchProducts} role="search" className="flex min-w-0 sm:w-80">
                                    <label htmlFor="product-search" className="sr-only">Search products</label>
                                    <input
                                        id="product-search"
                                        type="search"
                                        value={data.search}
                                        onChange={(event) => setData('search', event.target.value)}
                                        placeholder="Search products"
                                        className="min-w-0 flex-1 rounded-l-md border border-r-0 border-[#dce4df] bg-white px-3 py-2.5 text-sm text-[#25352e] outline-none placeholder:text-[#8b9790] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
                                    />
                                    <button
                                        type="submit"
                                        className="rounded-r-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                                    >
                                        Search
                                    </button>
                                </form>

                                <label className="flex items-center gap-2 text-sm text-[#69766f]">
                                    <span className="shrink-0">Sort by</span>
                                    <select
                                        value={data.sort}
                                        onChange={selectSort}
                                        className="min-w-40 rounded-md border border-[#dce4df] bg-white px-3 py-2.5 text-sm font-medium text-[#43524b] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15"
                                    >
                                        <option value="newest">Recently added</option>
                                        <option value="price_asc">Price: low to high</option>
                                        <option value="price_desc">Price: high to low</option>
                                        <option value="name">Name</option>
                                    </select>
                                </label>
                            </div>
                        </div>

                        <div className="mb-6 flex flex-wrap gap-2" aria-label="Product categories">
                            <button
                                type="button"
                                onClick={() => selectCategory(null)}
                                aria-pressed={!data.category}
                                className={`rounded-md px-3.5 py-2 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] ${!data.category ? 'bg-[#1d342d] text-white' : 'border border-[#dce4df] bg-white text-[#59675f] hover:bg-[#edf3ef]'}`}
                            >
                                All products
                            </button>
                            {categories.map((category) => (
                                <button
                                    key={category.id}
                                    type="button"
                                    onClick={() => selectCategory(category)}
                                    aria-pressed={data.category === String(category.id)}
                                    className={`rounded-md px-3.5 py-2 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] ${data.category === String(category.id) ? 'bg-[#1d342d] text-white' : 'border border-[#dce4df] bg-white text-[#59675f] hover:bg-[#edf3ef]'}`}
                                >
                                    {category.name}
                                </button>
                            ))}
                        </div>

                        {products.data.length > 0 ? (
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                {products.data.map((product) => (
                                    <ProductCard
                                        key={product.id}
                                        product={product}
                                        onAdd={addToCart}
                                        adding={addingProductId === product.id}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="border-y border-[#e4e9e4] py-16 text-center">
                                <p className="m-0 text-lg font-semibold text-[#25352e]">No products found</p>
                                <p className="mb-0 mt-2 text-sm text-[#718078]">Try another search or category.</p>
                                <Link href="/shop" className="mt-4 inline-block text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                                    Clear filters
                                </Link>
                            </div>
                        )}

                        {products.links.length > 3 && (
                            <nav aria-label="Pagination" className="mt-8 flex flex-wrap justify-center gap-2">
                                {products.links.slice(1, -1).map((link) => (
                                    link.url ? (
                                        <Link
                                            key={link.label}
                                            href={link.url}
                                            aria-current={link.active ? 'page' : undefined}
                                            className={`grid min-h-10 min-w-10 place-items-center rounded-md border px-3 text-sm font-semibold no-underline ${link.active ? 'border-[#237b65] bg-[#237b65] text-white' : 'border-[#dce4df] bg-white text-[#43524b] hover:bg-[#edf3ef]'}`}
                                        >
                                            {link.label}
                                        </Link>
                                    ) : null
                                ))}
                            </nav>
                        )}
                    </section>
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