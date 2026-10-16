import { Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from './AdminLayout';
import { formatValue, resourceDefinitions, resolveValue } from './resourceDefinitions';

export default function ResourceIndex({ resource, records, baseUrl, success }) {
    const [deletingId, setDeletingId] = useState(null);
    const { auth } = usePage().props;
    const permissions = auth?.user?.permissions ?? [];
    const roles = auth?.user?.roles ?? [];
    const canManageByRole = resource !== 'products' && roles.includes('admin');
    const canCreate = canManageByRole || permissions.includes(`create ${resource}`);
    const canEdit = canManageByRole || permissions.includes(`edit ${resource}`);
    const canDelete = canManageByRole || permissions.includes(`delete ${resource}`);
    const definition = resourceDefinitions[resource];

    const deleteRecord = (record) => {
        if (!window.confirm(definition.deleteConfirmation)) {
            return;
        }

        setDeletingId(record.id);
        router.delete(`${baseUrl}/${record.id}`, {
            preserveScroll: true,
            onFinish: () => setDeletingId(null),
        });
    };

    return (
        <AdminLayout title={definition.plural} success={success}>
            <div className="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
                    <h1 className="m-0 text-2xl font-bold text-[#26332f]">{definition.plural}</h1>
                    <p className="mb-0 mt-2 text-sm text-[#71807b]">{definition.description}</p>
                </div>
                {canCreate && (
                    <Link
                        href={`${baseUrl}/create`}
                        className="inline-flex w-fit items-center justify-center rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#1b6957]"
                    >
                        Add {definition.singular}
                    </Link>
                )}
            </div>

            <section aria-label={definition.plural} className="mt-8 overflow-hidden rounded-lg border border-[#e6ece9] bg-white shadow-sm">
                <div className="flex items-center justify-between border-b border-[#e6ece9] px-5 py-4">
                    <h2 className="m-0 text-sm font-semibold text-[#35443e]">
                        {resource === 'suppliers' ? 'Supplier directory' : resource === 'categories' ? 'Category list' : 'Inventory list'}
                    </h2>
                    <span className="text-xs font-medium tabular-nums text-[#71807b]">
                        {records.length} {definition.singular}{records.length === 1 ? '' : 's'}
                    </span>
                </div>

                {records.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-180 border-collapse text-left text-sm">
                            <thead className="bg-[#f8faf9] text-xs uppercase text-[#71807b]">
                                <tr>
                                    {definition.columns.map((column) => (
                                        <th key={column.label} scope="col" className="px-5 py-3 font-semibold">{column.label}</th>
                                    ))}
                                    <th scope="col" className="px-5 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#edf1ef]">
                                {records.map((record) => (
                                    <tr key={record.id} className="transition-colors hover:bg-[#fbfcfb]">
                                        {definition.columns.map((column) => {
                                            const value = resolveValue(record, column.path);

                                            return (
                                                <td key={column.label} className="px-5 py-4 text-[#5f6c67]">
                                                    {column.primary ? (
                                                        <Link href={`${baseUrl}/${record.id}`} className="font-semibold text-[#26332f] underline-offset-4 hover:text-[#237b65] hover:underline">
                                                            {formatValue(value, column)}
                                                        </Link>
                                                    ) : (
                                                        formatValue(value, column)
                                                    )}
                                                </td>
                                            );
                                        })}
                                        <td className="px-5 py-4">
                                            <div className="flex items-center justify-end gap-3 whitespace-nowrap">
                                                <Link href={`${baseUrl}/${record.id}`} className="font-medium text-[#237b65] underline-offset-4 hover:underline">
                                                    View
                                                </Link>
                                                {canEdit && (
                                                    <Link href={`${baseUrl}/${record.id}/edit`} className="font-medium text-[#43524c] underline-offset-4 hover:text-[#237b65] hover:underline">
                                                        Edit
                                                    </Link>
                                                )}
                                                {canDelete && (
                                                    <button
                                                        type="button"
                                                        disabled={deletingId === record.id}
                                                        onClick={() => deleteRecord(record)}
                                                        className="border-0 bg-transparent p-0 font-medium text-[#b94343] hover:underline disabled:cursor-wait disabled:opacity-60"
                                                    >
                                                        {deletingId === record.id ? 'Deleting' : 'Delete'}
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="px-5 py-14 text-center">
                        <h3 className="m-0 text-base font-semibold text-[#35443e]">{definition.emptyTitle}</h3>
                        <p className="mb-0 mt-2 text-sm text-[#71807b]">{definition.emptyDescription}</p>
                        {canCreate && (
                            <Link href={`${baseUrl}/create`} className="mt-5 inline-flex rounded-md bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957]">
                                Add {definition.singular}
                            </Link>
                        )}
                    </div>
                )}
            </section>
        </AdminLayout>
    );
}
