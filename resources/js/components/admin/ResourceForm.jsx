import { Link, useForm } from '@inertiajs/react';
import AdminLayout from './AdminLayout';
import { resourceDefinitions } from './resourceDefinitions';

const inputClassName = 'w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15';

export default function ResourceForm({ resource, item, baseUrl, categories = [], suppliers = [] }) {
    const definition = resourceDefinitions[resource];
    const isEditing = Boolean(item);
    const initialData = Object.fromEntries(definition.fields.map((field) => {
        const value = item?.[field.name];
        return [field.name, field.type === 'checkbox' ? Boolean(value) : value ?? field.defaultValue ?? ''];
    }));
    const { data, setData, post, put, processing, errors } = useForm(initialData);
    const title = `${isEditing ? 'Edit' : 'Add'} ${definition.singular}`;

    const submit = (event) => {
        event.preventDefault();

        if (isEditing) {
            put(`${baseUrl}/${item.id}`);
            return;
        }

        post(baseUrl);
    };

    const optionSets = { categories, suppliers };

    return (
        <AdminLayout title={title}>
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="mb-2 mt-0 text-xs font-semibold uppercase text-[#71807b]">
                        <Link href={baseUrl} className="text-[#237b65] hover:underline">{definition.plural}</Link>
                        <span className="px-1 text-[#9ba6a1]">/</span>
                        {isEditing ? `Edit ${definition.singular}` : `New ${definition.singular}`}
                    </p>
                    <h1 className="m-0 text-2xl font-bold text-[#26332f]">{title}</h1>
                    <p className="mb-0 mt-2 text-sm text-[#71807b]">
                        {isEditing ? `Update details for ${item.name}.` : `Enter details for the new ${definition.singular}.`}
                    </p>
                </div>
                <Link href={baseUrl} className="w-fit rounded-md border border-[#d8e0dc] bg-white px-3 py-2 text-sm font-medium text-[#43524c] hover:bg-[#f8faf9]">
                    Back to {definition.plural.toLowerCase()}
                </Link>
            </div>

            <section className="max-w-3xl rounded-lg border border-[#e6ece9] bg-white p-5 shadow-sm sm:p-7">
                {Object.keys(errors).length > 0 && (
                    <div role="alert" className="mb-6 rounded-md border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p className="m-0 font-semibold">Please correct the highlighted fields.</p>
                    </div>
                )}

                <form onSubmit={submit} className="flex flex-col gap-5">
                    <div className="grid gap-5 sm:grid-cols-2">
                        {definition.fields.map((field) => {
                            const error = errors[field.name];
                            const fieldId = `${resource}-${field.name}`;
                            const fieldClassName = `${field.wide ? 'sm:col-span-2 ' : ''}min-w-0`;

                            if (field.type === 'checkbox') {
                                return (
                                    <div key={field.name} className={fieldClassName}>
                                        <label htmlFor={fieldId} className="inline-flex cursor-pointer items-center gap-2 rounded-md border border-[#d8e0dc] bg-[#f8faf9] px-3 py-2.5 text-sm font-medium text-[#35443e]">
                                            <input
                                                id={fieldId}
                                                name={field.name}
                                                type="checkbox"
                                                checked={Boolean(data[field.name])}
                                                onChange={(event) => setData(field.name, event.target.checked)}
                                            />
                                            {field.label}
                                        </label>
                                        {error && <p role="alert" className="mb-0 mt-1.5 text-sm text-red-700">{error}</p>}
                                    </div>
                                );
                            }

                            return (
                                <div key={field.name} className={fieldClassName}>
                                    <label htmlFor={fieldId} className="mb-1.5 block text-sm font-medium text-[#35443e]">
                                        {field.label}
                                        {field.optional && <span className="font-normal text-[#818c88]"> (optional)</span>}
                                    </label>
                                    {field.type === 'textarea' ? (
                                        <textarea
                                            id={fieldId}
                                            name={field.name}
                                            rows="4"
                                            value={data[field.name]}
                                            onChange={(event) => setData(field.name, event.target.value)}
                                            aria-invalid={Boolean(error)}
                                            aria-describedby={error ? `${fieldId}-error` : undefined}
                                            className={`${inputClassName} resize-y`}
                                        />
                                    ) : field.type === 'select' ? (
                                        <select
                                            id={fieldId}
                                            name={field.name}
                                            required={field.required}
                                            value={data[field.name] === '' ? '' : String(data[field.name])}
                                            onChange={(event) => setData(field.name, event.target.value)}
                                            aria-invalid={Boolean(error)}
                                            aria-describedby={error ? `${fieldId}-error` : undefined}
                                            className={inputClassName}
                                        >
                                            <option value="">{field.emptyLabel}</option>
                                            {optionSets[field.options].map((option) => (
                                                <option key={option.id} value={option.id}>{option.name}</option>
                                            ))}
                                        </select>
                                    ) : (
                                        <input
                                            id={fieldId}
                                            name={field.name}
                                            type={field.type}
                                            required={field.required}
                                            min={field.min}
                                            step={field.step}
                                            value={data[field.name]}
                                            onChange={(event) => setData(field.name, event.target.value)}
                                            aria-invalid={Boolean(error)}
                                            aria-describedby={error ? `${fieldId}-error` : undefined}
                                            className={inputClassName}
                                        />
                                    )}
                                    {error && <p id={`${fieldId}-error`} role="alert" className="mb-0 mt-1.5 text-sm text-red-700">{error}</p>}
                                </div>
                            );
                        })}
                    </div>

                    <div className="flex flex-wrap justify-end gap-3 border-t border-[#edf1ef] pt-5">
                        <Link href={baseUrl} className="rounded-md border border-[#d8e0dc] bg-white px-4 py-2.5 text-sm font-medium text-[#43524c] hover:bg-[#f8faf9]">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md border-0 bg-[#237b65] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1b6957] disabled:cursor-wait disabled:opacity-60"
                        >
                            {processing ? 'Saving...' : isEditing ? 'Save changes' : `Save ${definition.singular}`}
                        </button>
                    </div>
                </form>
            </section>
        </AdminLayout>
    );
}
