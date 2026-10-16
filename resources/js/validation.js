export function validateField(field, value, values = {}) {
    const text = value === null || value === undefined ? '' : String(value);
    const trimmed = text.trim();

    if (field.required && trimmed === '') {
        return `${field.label} is required.`;
    }

    if (trimmed === '') {
        return null;
    }

    if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed)) {
        return 'Enter a valid email address.';
    }

    if (field.minLength && text.length < field.minLength) {
        return `${field.label} must be at least ${field.minLength} characters.`;
    }

    if (field.maxLength && text.length > field.maxLength) {
        return `${field.label} may not be greater than ${field.maxLength} characters.`;
    }

    if (field.type === 'number') {
        const number = Number(value);

        if (!Number.isFinite(number)) {
            return `${field.label} must be a number.`;
        }

        if (field.integer && !Number.isInteger(number)) {
            return `${field.label} must be a whole number.`;
        }

        if (field.min !== undefined && number < Number(field.min)) {
            return `${field.label} must be at least ${field.min}.`;
        }
    }

    if (field.matches && value !== values[field.matches]) {
        return `${field.label} must match the password.`;
    }

    return null;
}