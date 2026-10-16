import { Link, usePage } from '@inertiajs/react';
import AdminLayout from './AdminLayout';
import { formatValue, resourceDefinitions, resolveValue } from './resourceDefinitions';

export default function ResourceShow({ resource, item, baseUrl, productsUrl }) {
    const { auth } = usePage().props;
    const definition = resourceDefinitions[resource];
    const canEdit = (auth?.user?.permissions ?? []).includes(`edit ${resource}`);
    const relatedProducts = item.products ?? [];

    return (
        <AdminLayout title={item.name}>
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                        <Link href={baseUrl} className="text-[#237b65] hover:underline">{definition.plural}</Link>
                        <span className="px-1 text-[#9ba6a1]">/</span>
                        {definition.singular} details
                    </p>
                    <h1 className="m-0 text-2xl font-bold text-[#26332f]">{item.name}</h1>
                    {resource === 'categories' && (
                        <p className="mb-0 mt-2 text-sm text-[#71807b]">{item.description || 'No description provided.'}</p>
                    )}
                </div>
                <div className="flex flex-wrap gap-3">
                    <Link href={baseUrl} className="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] hover:bg-[#f8faf9]">
                        Back to {definition.plural.toLowerCase()}
                    </Link>
                    {canEdit && (
                        <Link href={`${baseUrl}/${item.id}/edit`} className="rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957]">
                            Edit {definition.singular}
                        </Link>
                    )}
                </div>
            </div>

            {resource === 'products' && (
                <div className="mb-4 flex items-center justify-between rounded-lg border border-[#e6ece9] bg-white px-5 py-4">
                    <h2 className="m-0 text-base font-semibold text-[#26332f]">Product details</h2>
                    <span className={`rounded-full px-2.5 py-1 text-xs font-semibold ${item.stock <= 5 ? 'bg-orange-50 text-orange-800' : 'bg-[#e7f2ef] text-[#27644f]'}`}>
                        {item.stock} {item.stock <= 5 ? 'units · low stock' : 'units in stock'}
                    </span>
                </div>
            )}

            <section aria-label={`${definition.singular} details`} className="overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
                {resource === 'suppliers' && (
                    <div className="border-b border-[#edf1ef] px-5 py-5 sm:px-7">
                        <p className="mb-1 mt-0 text-xs font-semibold uppercase text-[#818c88]">Supplier profile</p>
                        <h2 className="m-0 text-lg font-semibold text-[#26332f]">{item.name}</h2>
                    </div>
                )}
                <dl className="m-0 grid gap-x-8 sm:grid-cols-2">
                    {definition.details.map((field) => (
                        <div key={field.label} className={`border-b border-[#edf1ef] px-5 py-4 sm:px-7 ${field.wide ? 'sm:col-span-2' : ''}`}>
                            <dt className="text-xs font-semibold uppercase text-[#818c88]">{field.label}</dt>
                            <dd className={`mb-0 mt-1 text-sm font-medium text-[#35443e] ${field.wide ? 'whitespace-pre-line leading-6' : ''}`}>
                                {formatValue(resolveValue(item, field.path), field)}
                            </dd>
                        </div>
                    ))}
                </dl>
            </section>

            {definition.relatedProductsTitle && (
                <section aria-label={definition.relatedProductsTitle} className="mt-8 overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
                        <h2 className="m-0 text-sm font-semibold text-[#35443e]">{definition.relatedProductsTitle}</h2>
                        <span className="text-xs font-medium tabular-nums text-[#71807b]">{relatedProducts.length} products</span>
                    </div>
                    {relatedProducts.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-112.5 border-collapse text-left text-sm">
                                <thead className="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                                    <tr>
                                        <th scope="col" className="px-5 py-3 font-semibold">Product</th>
                                        <th scope="col" className="px-5 py-3 font-semibold">{definition.relatedProductColumn.label}</th>
                                        <th scope="col" className="px-5 py-3 font-semibold">Stock</th>
                                        <th scope="col" className="px-5 py-3 text-right font-semibold">Price</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#edf1ef]">
                                    {relatedProducts.map((product) => (
                                        <tr key={product.id} className="transition-colors hover:bg-[#fbfcfb]">
                                            <td className="px-5 py-4">
                                                <Link href={`${productsUrl}/${product.id}`} className="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">
                                                    {product.name}
                                                </Link>
                                            </td>
                                            <td className="px-5 py-4 text-[#5f6c67]">
                                                {formatValue(resolveValue(product, definition.relatedProductColumn.path), definition.relatedProductColumn)}
                                            </td>
                                            <td className="px-5 py-4 tabular-nums text-[#5f6c67]">{product.stock}</td>
                                            <td className="whitespace-nowrap px-5 py-4 text-right font-medium tabular-nums text-[#35443e]">
                                                {formatValue(product.price, { format: 'currency' })}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="m-0 px-5 py-10 text-sm text-[#71807b]">{definition.emptyProductsDescription}</p>
                    )}
                </section>
            )}
        </AdminLayout>
    );
}
