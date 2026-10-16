import { Head } from '@inertiajs/react';
import AdminLayout from '../../components/admin/AdminLayout';

const alertStyles = {
    expired: 'border-red-300 bg-red-50 text-red-950',
    expiring_soon: 'border-amber-300 bg-amber-50 text-amber-950',
    low_stock: 'border-orange-300 bg-orange-50 text-orange-950',
};

const alertLabels = {
    expired: 'Expiration alert',
    expiring_soon: 'Near expiration',
    low_stock: 'Low stock',
};

export default function NotificationsIndex({ alerts }) {
    return (
        <AdminLayout title="Notifications">
            <Head title="Notifications | RxStock" />

            <div className="mx-auto max-w-4xl">
                <header className="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">Inventory</p>
                        <h1 className="m-0 text-2xl font-bold text-[#26332f]">Notifications</h1>
                        <p className="mb-0 mt-2 text-sm text-[#71807b]">Stock and expiration alerts for your inventory.</p>
                    </div>
                    <span className="w-fit rounded-full border border-[#dce5e1] bg-white px-3 py-1.5 text-xs font-semibold tabular-nums text-[#43524c]">
                        {alerts.length} {alerts.length === 1 ? 'alert' : 'alerts'}
                    </span>
                </header>

                {alerts.length > 0 ? (
                    <ul aria-label="Inventory notifications" aria-live="polite" className="m-0 flex list-none flex-col gap-3 p-0">
                        {alerts.map((alert, index) => (
                            <li
                                key={`${alert.type}-${index}`}
                                role="status"
                                className={`rounded-md border-l-4 px-5 py-4 shadow-sm ${alertStyles[alert.type] ?? 'border-gray-400 bg-gray-50 text-gray-900'}`}
                            >
                                <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-5">
                                    <div>
                                        <p className="m-0 text-xs font-bold uppercase">{alertLabels[alert.type] ?? 'Inventory alert'}</p>
                                        <h2 className="mb-0 mt-1 text-base font-semibold">{alert.title}</h2>
                                        <p className="mb-0 mt-1 text-sm leading-5">{alert.message}</p>
                                    </div>
                                </div>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <section className="rounded-md border border-[#e6ece9] bg-white px-5 py-12 text-center">
                        <h2 className="m-0 text-base font-semibold text-[#35443e]">No inventory alerts</h2>
                        <p className="mb-0 mt-2 text-sm text-[#71807b]">There are no low-stock or expiration alerts right now.</p>
                    </section>
                )}
            </div>
        </AdminLayout>
    );
}