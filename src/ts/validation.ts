/**
 * validation.ts
 * Client-side field validation utilities and text formatters.
 * These functions are used by both the core checkout script and the
 * Brazilian fields validation script.
 */

/**
 * Validate an email address using a simple RFC-compliant regex.
 */
export function isValidEmail(value: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
}

/**
 * Validate a Brazilian CPF (Cadastro de Pessoas Físicas).
 * Strips non-digit characters before validation.
 */
export function isValidCPF(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    if (digits.length !== 11 || /^(\d)\1{10}$/.test(digits)) return false;

    let sum = 0;
    for (let i = 0; i < 9; i++) sum += parseInt(digits[i]) * (10 - i);
    let remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(digits[9])) return false;

    sum = 0;
    for (let i = 0; i < 10; i++) sum += parseInt(digits[i]) * (11 - i);
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    return remainder === parseInt(digits[10]);
}

/**
 * Validate a Brazilian CNPJ (Cadastro Nacional da Pessoa Jurídica).
 * Strips non-digit characters before validation.
 */
export function isValidCNPJ(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    if (digits.length !== 14 || /^(\d)\1{13}$/.test(digits)) return false;

    const calcDigit = (d: string, weights: number[]): number => {
        const sum = weights.reduce((acc, w, i) => acc + parseInt(d[i]) * w, 0);
        const remainder = sum % 11;
        return remainder < 2 ? 0 : 11 - remainder;
    };

    const w1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    const w2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    return (
        calcDigit(digits, w1) === parseInt(digits[12]) &&
        calcDigit(digits, w2) === parseInt(digits[13])
    );
}

/**
 * Validate a Brazilian phone number (accepts 10 or 11 digits after stripping formatting).
 */
export function isValidBRPhone(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    return digits.length === 10 || digits.length === 11;
}

/**
 * Validate a Brazilian postcode (CEP) — 8 digits.
 */
export function isValidBRPostcode(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    return digits.length === 8;
}

/**
 * Format text input with title-case capitalization.
 */
export function capitalizeWords(value: string): string {
    return value
        .split(' ')
        .map((word) =>
            word.length > 0
                ? word[0].toUpperCase() + word.slice(1).toLowerCase()
                : word
        )
        .join(' ');
}

/**
 * Show a field error message in the dedicated <span data-error-for="fieldId"> element.
 * If no such element exists the error is silently ignored.
 */
export function showFieldError(fieldId: string, message: string): void {
    const el = document.querySelector<HTMLSpanElement>(
        `span[data-error-for="${fieldId}"]`
    );
    if (el) {
        el.textContent = message;
        el.removeAttribute('hidden');
        el.removeAttribute('aria-hidden');
    }
}

/**
 * Clear a field error previously set by showFieldError.
 */
export function clearFieldError(fieldId: string): void {
    const el = document.querySelector<HTMLSpanElement>(
        `span[data-error-for="${fieldId}"]`
    );
    if (el) {
        el.textContent = '';
        el.setAttribute('hidden', '');
        el.setAttribute('aria-hidden', 'true');
    }
}

