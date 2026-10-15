const categoryArtwork = [
    {
        match: /vitamin|supplement|wellness/i,
        image: 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?auto=format&fit=crop&w=900&q=85',
    },
    {
        match: /skin|beauty|personal care/i,
        image: 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=85',
    },
    {
        match: /medicine|pain|first aid/i,
        image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=900&q=85',
    },
];

const defaultArtwork = 'https://images.unsplash.com/photo-1576602976047-174e57a47881?auto=format&fit=crop&w=900&q=85';
const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });

export default function ProductCard({ product, onAdd, adding, isWishlisted, onToggleWishlist, wishlistBusy }) {
    const categoryName = product.category?.name ?? 'Pharmacy';
    const artwork = categoryArtwork.find(({ match }) => match.test(categoryName))?.image ?? defaultArtwork;

    return (
        <article className="group min-w-0 overflow-hidden rounded-lg border border-[#e4e9e4] bg-white transition-shadow hover:shadow-[0_12px_30px_rgba(31,55,45,0.10)]">
            <div className="relative aspect-[4/3] overflow-hidden bg-[#edf2ed]">
                <img
                    src={artwork}
                    alt={`${categoryName} collection`}
                    loading="lazy"
                    className="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                />
                <span className="absolute left-3 top-3 rounded-sm bg-white/95 px-2.5 py-1 text-[11px] font-semibold uppercase text-[#43524b]">
                    {categoryName}
                </span>
            </div>

            <div className="flex min-h-44 flex-col p-4">
                <div className="flex items-start justify-between gap-3">
                    <h2 className="m-0 line-clamp-2 text-base font-semibold leading-6 text-[#25352e]">
                        {product.name}
                    </h2>
                    <span className="mt-1 size-2 shrink-0 rounded-full bg-[#348568]" aria-label="In stock" title="In stock" />
                </div>
                <p className="mb-0 mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-[#718078]">
                    {product.description || 'A pharmacy essential, ready for your routine.'}
                </p>
                <button
                    type="button"
                    onClick={() => onToggleWishlist(product)}
                    aria-pressed={isWishlisted}
                    disabled={wishlistBusy}
                    className="mt-3 self-start border-0 bg-transparent p-0 text-sm font-semibold text-[#237b65] underline-offset-4 hover:underline disabled:cursor-wait disabled:opacity-60"
                >
                    {wishlistBusy ? 'Saving...' : isWishlisted ? 'Saved to wishlist' : 'Save to wishlist'}
                </button>
                <div className="mt-auto flex items-end justify-between gap-3 pt-4">
                    <p className="m-0 text-lg font-bold tabular-nums text-[#1d342d]">
                        {peso.format(Number(product.price))}
                    </p>
                    <span className={`text-xs font-medium ${product.stock > 0 ? 'text-[#718078]' : 'text-[#aa4c3c]'}`}>
                        {product.stock > 0 ? `${product.stock} available` : 'Sold out'}
                    </span>
                </div>
                <button
                    type="button"
                    onClick={() => onAdd(product)}
                    disabled={product.stock < 1 || adding}
                    className="mt-4 w-full rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#237b65] disabled:cursor-not-allowed disabled:bg-[#dce4df] disabled:text-[#718078]"
                >
                    {product.stock < 1 ? 'Out of stock' : adding ? 'Adding...' : 'Add to cart'}
                </button>
            </div>
        </article>
    );
}