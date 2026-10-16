export const resourceDefinitions = {
    products: {
        singular: 'product',
        plural: 'Products',
        description: 'Review stock levels, categories, and expiration dates.',
        emptyTitle: 'No products yet',
        emptyDescription: 'Add your first product to start tracking inventory.',
        deleteConfirmation: 'Delete this product?',
        fields: [
            { name: 'name', label: 'Product name', type: 'text', required: true, wide: true },
            { name: 'product_category_id', label: 'Category', type: 'select', required: true, options: 'categories', emptyLabel: 'Select a category' },
            { name: 'supplier_id', label: 'Supplier', type: 'select', options: 'suppliers', emptyLabel: 'Unassigned', optional: true },
            { name: 'price', label: 'Price', type: 'number', required: true, min: '0', step: '0.01' },
            { name: 'stock', label: 'Stock', type: 'number', required: true, min: '0' },
            { name: 'reorder_threshold', label: 'Reorder threshold', type: 'number', required: true, min: '0', defaultValue: 5 },
            { name: 'dosage', label: 'Dosage', type: 'text', optional: true },
            { name: 'strength', label: 'Strength', type: 'text', optional: true },
            { name: 'expiration_date', label: 'Expiration date', type: 'date', optional: true },
            { name: 'requires_prescription', label: 'Requires prescription', type: 'checkbox', wide: true },
            { name: 'description', label: 'Description', type: 'textarea', optional: true, wide: true },
        ],
        columns: [
            { label: 'Product', path: 'name', primary: true },
            { label: 'Category', path: 'category.name', fallback: 'Uncategorized' },
            { label: 'Supplier', path: 'supplier.name', fallback: 'Unassigned' },
            { label: 'Price', path: 'price', format: 'currency' },
            { label: 'Stock', path: 'stock', format: 'stock' },
            { label: 'Reorder threshold', path: 'reorder_threshold', format: 'units' },
            { label: 'Expiration', path: 'expiration_date', format: 'date', fallback: 'Not set' },
        ],
        details: [
            { label: 'Category', path: 'category.name', fallback: 'Uncategorized' },
            { label: 'Supplier', path: 'supplier.name', fallback: 'Unassigned' },
            { label: 'Price', path: 'price', format: 'currency' },
            { label: 'Dosage', path: 'dosage', fallback: 'Not specified' },
            { label: 'Strength', path: 'strength', fallback: 'Not specified' },
            { label: 'Stock on hand', path: 'stock', format: 'units' },
            { label: 'Reorder threshold', path: 'reorder_threshold', format: 'units' },
            { label: 'Expiration date', path: 'expiration_date', format: 'date', fallback: 'Not set' },
            { label: 'Prescription status', path: 'requires_prescription', format: 'prescription' },
            { label: 'Description', path: 'description', fallback: 'No description provided.', wide: true },
        ],
    },
    categories: {
        singular: 'category',
        plural: 'Product categories',
        description: 'Organize inventory and see how many products belong to each category.',
        emptyTitle: 'No categories yet',
        emptyDescription: 'Create a category before adding products.',
        deleteConfirmation: 'Delete this category? Its products will also be deleted.',
        fields: [
            { name: 'name', label: 'Category name', type: 'text', required: true },
            { name: 'description', label: 'Description', type: 'textarea', optional: true, wide: true },
        ],
        columns: [
            { label: 'Category', path: 'name', primary: true },
            { label: 'Description', path: 'description', fallback: 'No description' },
            { label: 'Products', path: 'products_count' },
        ],
        details: [
            { label: 'Products', path: 'products.length' },
        ],
        relatedProductsTitle: 'Products in this category',
        relatedProductColumn: { label: 'Supplier', path: 'supplier.name', fallback: 'Unassigned' },
        emptyProductsDescription: 'There are no products in this category yet.',
    },
    suppliers: {
        singular: 'supplier',
        plural: 'Suppliers',
        description: 'Manage supplier contacts and the products they provide.',
        emptyTitle: 'No suppliers yet',
        emptyDescription: 'Add a supplier to connect products with their source.',
        deleteConfirmation: 'Delete this supplier? Associated products will become unassigned.',
        fields: [
            { name: 'name', label: 'Supplier name', type: 'text', required: true, wide: true },
            { name: 'contact_person', label: 'Contact person', type: 'text', optional: true },
            { name: 'email', label: 'Email', type: 'email', optional: true },
            { name: 'phone', label: 'Phone', type: 'tel', optional: true },
            { name: 'address', label: 'Address', type: 'textarea', optional: true, wide: true },
        ],
        columns: [
            { label: 'Supplier', path: 'name', primary: true },
            { label: 'Contact person', path: 'contact_person', fallback: 'Not set' },
            { label: 'Email', path: 'email', fallback: 'No email' },
            { label: 'Phone', path: 'phone', fallback: 'No phone' },
            { label: 'Products', path: 'products_count' },
        ],
        details: [
            { label: 'Contact person', path: 'contact_person', fallback: 'Not set' },
            { label: 'Email', path: 'email', fallback: 'Not set' },
            { label: 'Phone', path: 'phone', fallback: 'Not set' },
            { label: 'Address', path: 'address', fallback: 'Not set', wide: true },
            { label: 'Products', path: 'products.length' },
        ],
        relatedProductsTitle: 'Supplied products',
        relatedProductColumn: { label: 'Category', path: 'category.name', fallback: 'Uncategorized' },
        emptyProductsDescription: 'No products are assigned to this supplier yet.',
    },
};

export function resolveValue(record, path) {
    return path.split('.').reduce((value, key) => value?.[key], record);
}

export function formatValue(value, field) {
    if (value === null || value === undefined || value === '') {
        return field.fallback ?? 'Not set';
    }

    if (field.format === 'currency') {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
        }).format(Number(value));
    }

    if (field.format === 'date') {
        const date = new Date(value.includes('T') ? value : `${value}T00:00:00`);
        return Number.isNaN(date.getTime())
            ? field.fallback ?? 'Not set'
            : new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(date);
    }

    if (field.format === 'units') {
        return `${value} units`;
    }

    if (field.format === 'prescription') {
        return value ? 'Prescription required' : 'No prescription required';
    }

    return value;
}
