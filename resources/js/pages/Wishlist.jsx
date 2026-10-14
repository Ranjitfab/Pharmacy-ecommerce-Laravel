import { Head, Link, router } from '@inertiajs/react';
import ShopHeader from '../components/ShopHeader';

const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

export default function Wishlist({ customerName, products, wishlistCount, success }) {
    const removeFromWishlist = (product) => {
        router.delete(`/wishlist/${product.id}`, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title="Wishlist" />
            <div className="min-h-screen bg-[#f7f9f6] text-[#25352e]">
                <ShopHeader customerName={customerName} wishlistCount={wishlistCount} />

                <main className="mx-auto max-w-7xl px-5 pb-16 pt-8 sm:px-8 sm:pt-12">
                    {success && (
                        <p role="status" className="mb-6 rounded-md border border-[#b8d9ca] bg-[#e9f5ee] px-4 py-3 text-sm font-medium text-[#245c43]">
                            {success}
                        </p>
                    )}

                    <div className="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p className="mb-2 mt-0 text-xs font-bold uppercase text-[#237b65]">RxStock · Saved products</p>
                            <h1 className="m-0 text-3xl font-bold text-[#1d342d]">My wishlist</h1>
                            <p className="mb-0 mt-2 text-sm text-[#718078]">
                                {products.length} {products.length === 1 ? 'saved product' : 'saved products'}
                            </p>
                        </div>
                        <Link href="/shop" className="text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                            Continue shopping
                        </Link>
                    </div>

                    {products.length > 0 ? (
                        <ul className="m-0 grid list-none gap-4 p-0 sm:grid-cols-2 lg:grid-cols-3">
                            {products.map((product) => (
                                <li key={product.id} className="flex flex-col rounded-lg border border-[#e4e9e4] bg-white p-5 shadow-sm">
                                    <div className="mb-3 flex items-start justify-between gap-4">
                                        <div>
                                            <p className="mb-1 mt-0 text-xs font-semibold uppercase text-[#718078]">
                                                {product.category?.name ?? 'Pharmacy'}
                                            </p>
                                            <h2 className="m-0 text-lg font-semibold text-[#25352e]">{product.name}</h2>
                                        </div>
                                        <span className={`shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ${product.stock > 0 ? 'bg-[#e9f5ee] text-[#245c43]' : 'bg-orange-50 text-orange-800'}`}>
                                            {product.stock > 0 ? 'In stock' : 'Sold out'}
                                        </span>
                                    </div>

                                    <p className="mb-4 mt-0 line-clamp-2 text-sm leading-5 text-[#718078]">
                                        {product.description || 'A pharmacy essential.'}
                                    </p>

                                    <div className="mb-5 flex flex-wrap gap-x-4 gap-y-1 text-sm text-[#53615c]">
                                        {product.dosage && <span>Dosage: {product.dosage}</span>}
                                        {product.strength && <span>Strength: {product.strength}</span>}
                                        {product.requires_prescription && <span>Prescription required</span>}
                                    </div>

                                    <div className="mt-auto flex items-center justify-between gap-4 border-t border-[#edf1ef] pt-4">
                                        <span className="text-lg font-bold tabular-nums text-[#1d342d]">
                                            {peso.format(Number(product.price))}
                                        </span>
                                        <button
                                            type="button"
                                            onClick={() => removeFromWishlist(product)}
                                            className="rounded-md border border-[#dce4df] bg-white px-3 py-2 text-sm font-semibold text-[#43524b] transition-colors hover:border-[#b9cec3] hover:bg-[#f7faf8] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65]"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="border-y border-[#e4e9e4] py-16 text-center">
                            <p className="m-0 text-lg font-semibold text-[#25352e]">Your wishlist is empty</p>
                            <p className="mb-0 mt-2 text-sm text-[#718078]">Save products from the shop to find them here later.</p>
                            <Link href="/shop" className="mt-4 inline-block text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline">
                                Browse products
                            </Link>
                        </div>
                    )}
                </main>
            </div>
        </>
    );
}
