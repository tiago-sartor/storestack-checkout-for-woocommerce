/**
 * validation.ts
 * Client-side field validation utilities and text formatters.
 */

declare global {
    interface Window {
        storeStackCheckoutValidation: {
            messages: {
                required: string;
                invalid: string;
            };
        };
    }
}

type ErrorType = 'required' | 'invalid';

/**
 * Show a field error message in the dedicated <span data-error-for="fieldId"> element.
 * If no such element exists the error is silently ignored.
 *
 * @param type - The kind of validation error to display.
 * @param fieldId - The field ID used to locate its error message element.
 * @param label - The human-readable field label inserted into the message.
 * @returns Nothing.
 */
export function showError(type: ErrorType, fieldId: string, label: string): void {
    const el = Array.from(document.querySelectorAll<HTMLSpanElement>('span[data-error-for]'))
        .find((candidate) => candidate.dataset.errorFor === fieldId);

    if (el) {
        const message = formatErrorMessage(type, label);
        el.textContent = message;
        el.removeAttribute('hidden');
        el.removeAttribute('aria-hidden');
    }
}

/**
 * Format a localized validation message for a field label.
 *
 * @param type - The kind of validation error to format.
 * @param label - The human-readable field label to insert.
 * @returns The formatted validation message.
 */
function formatErrorMessage(type: ErrorType, label: string): string {
    return window.storeStackCheckoutValidation.messages[type].replace('%s', label);
}

/**
 * Clear a field error previously set by showError.
 *
 * @param fieldId - The field ID used to locate its error message element.
 * @returns Nothing.
 */
export function clearError(fieldId: string): void {
    const el = Array.from(document.querySelectorAll<HTMLSpanElement>('span[data-error-for]'))
        .find((candidate) => candidate.dataset.errorFor === fieldId);

    if (el) {
        el.textContent = '';
        el.setAttribute('hidden', '');
        el.setAttribute('aria-hidden', 'true');
    }
}

/**
 * Validate an email address using a simple RFC-compliant regex.
 *
 * @param value - The email address to validate.
 * @returns Whether the value matches the expected email format.
 */
export function isValidEmail(value: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
}

/**
 * Validate a Brazilian CPF (Cadastro de Pessoas Físicas).
 * Strips non-digit characters before validation.
 *
 * @param value - The CPF value to validate.
 * @returns Whether the CPF has valid length and check digits.
 */
export function isValidCPF(value: string): boolean {
    const cpf = value.replace(/\D/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    /**
     * Calculate a CPF check digit from a digit slice and starting weight.
     *
     * @param cpfSlice - The CPF digits included in the weighted sum.
     * @param weight - The starting weight applied to the first digit.
     * @returns The calculated check digit.
     */
    const weightedSum = (cpfSlice: string, weight: number): number => {
        let total = 0;
        for (let i = 0; i < cpfSlice.length; i++) {
            const digit = Number(cpfSlice.charAt(i));
            total += digit * (weight - i);
        }
        let remainder = (total * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        return remainder;
    }

    // Calculate first check digit (for positions 1..9)
    const firstDigit = weightedSum(cpf.substring(0, 9), 10);
    if (firstDigit !== Number(cpf.charAt(9))) {
        return false;
    }

    // Calculate second check digit (for positions 1..10)
    const secondDigit = weightedSum(cpf.substring(0, 10), 11);
    if (secondDigit !== Number(cpf.charAt(10))) {
        return false;
    }

    return true;
}

/**
 * Validate a Brazilian CNPJ (Cadastro Nacional da Pessoa Jurídica).
 * Strips non-alphanumeric characters before validation.
 *
 * @param value - The CNPJ value to validate.
 * @returns Whether the CNPJ has valid length and check digits.
 */
export function isValidCNPJ(value: string): boolean {
    const cnpj = String(value).toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (cnpj.length !== 14 || /^([A-Z0-9])\1+$/.test(cnpj)) return false;

    const base = cnpj.substring(0, 12);

    /**
     * Calculate a CNPJ check digit from a character slice and its weights.
     *
     * @param cnpjSlice - The CNPJ characters included in the weighted sum.
     * @param weights - The weights applied to each character.
     * @returns The calculated check digit.
     */
    const weightedSum = (cnpjSlice: string, weights: number[]): number => {
        let total = 0;
        for (let i = 0; i < cnpjSlice.length; i++) {
            const char = cnpjSlice[i];
            const value = char.charCodeAt(0) - 48;
            total += value * weights[i];
        }
        const remainder = total % 11;
        return remainder < 2 ? 0 : 11 - remainder;
    };

    const firstDigitWeights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    const secondDigitWeights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    // Calculate first check digit.
    const firstDigit = weightedSum(base, firstDigitWeights);

    // Append first check digit to the input and calculate second check digit.
    const secondDigit = weightedSum(base + firstDigit.toString(), secondDigitWeights);

    // Return both check digits as a string and compare with the last two digits of the input.
    if ((firstDigit.toString() + secondDigit.toString()) === cnpj.substring(12)) {
        return true;
    }

    return false;
}

/**
 * Validate a Brazilian phone number (accepts 10 or 11 digits after stripping formatting).
 *
 * @param value - The phone number to validate.
 * @returns Whether the number contains 10 or 11 digits.
 */
export function isValidBRPhone(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    return digits.length === 10 || digits.length === 11;
}

/**
 * Validate a Brazilian postcode (CEP) — 8 digits.
 *
 * @param value - The postcode to validate.
 * @returns Whether the postcode contains exactly 8 digits.
 */
export function isValidBRPostcode(value: string): boolean {
    const digits = value.replace(/\D/g, '');
    return digits.length === 8;
}

/**
 * Format text input with title-case capitalization.
 *
 * @param value - The text to normalize and capitalize.
 * @returns The normalized text with each word capitalized.
 */
export function capitalizeWords(value: string): string {
    return value
        .trim()
        .replace(/\s+/g, ' ')
        .split(' ')
        .map((word) =>
            word.length > 0
                ? word[0].toUpperCase() + word.slice(1).toLowerCase()
                : word
        )
        .join(' ');
}

type CheckoutField = HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;

interface BrazilianFieldConfig {
    id: string;
    validate?: (value: string) => boolean;
}

const REQUIRED_FIELD_SELECTOR =
    'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]):not([type="image"]), select, textarea';

const brazilianConfigFields: BrazilianFieldConfig[] = [
    {
        id: 'billing_cpf',
        validate: isValidCPF,
    },
    {
        id: 'billing_cnpj',
        validate: isValidCNPJ,
    },
    {
        id: 'billing_phone',
        validate: isValidBRPhone,
    },
    {
        id: 'billing_cellphone',
        validate: isValidBRPhone,
    },
    {
        id: 'billing_postcode',
        validate: isValidBRPostcode,
    },
    {
        id: 'shipping_postcode',
        validate: isValidBRPostcode,
    },
];

/**
 * Determine whether an element is an enabled, supported checkout field that is not hidden.
 *
 * @param element - The element to check.
 * @returns Whether the element is an eligible checkout field.
 */
function isEligibleField(element: Element | null): element is CheckoutField {
    if (!(element instanceof HTMLInputElement || element instanceof HTMLSelectElement || element instanceof HTMLTextAreaElement)) {
        return false;
    }

    if (element.disabled || (element instanceof HTMLInputElement &&
        ['hidden', 'submit', 'button', 'reset', 'image'].includes(element.type))) {
        return false;
    }

    // Check if the field wrapper is hidden via display: 'none'.
    const formRow = element.closest('p.form-row') ?? element;
    let ancestor: Element | null = element;
    while (ancestor) {
        if (getComputedStyle(ancestor).display === 'none') {
            return false;
        }
        if (ancestor === formRow) {
            break;
        }
        ancestor = ancestor.parentElement;
    }

    return true;
}

/**
 * Determine whether a checkout field has a meaningful value.
 *
 * @param field - The checkout field to inspect.
 * @param form - The checkout form used to find related radio buttons.
 * @returns Whether the field or its associated radio group has a value.
 */
function fieldHasValue(field: CheckoutField, form: HTMLFormElement): boolean {
    if (field instanceof HTMLInputElement) {
        if (field.type === 'checkbox') return field.checked;
        if (field.type === 'radio') {
            return Array.from(form.querySelectorAll<HTMLInputElement>('input[type="radio"]'))
                .some((radio) => radio.name === field.name && radio.checked);
        }
        if (field.type === 'file') return field.files !== null && field.files.length > 0;
    }

    if (field instanceof HTMLSelectElement && field.multiple) {
        return Array.from(field.selectedOptions).some((option) => option.value !== '');
    }

    return field.value.trim() !== '';
}

/**
 * Get a normalized human-readable label for a checkout field.
 *
 * @param field - The checkout field whose label is needed.
 * @returns The associated label text, or an empty string if none is found.
 */
function fieldLabel(field: CheckoutField): string {
    const associatedLabel = Array.from(field.labels ?? [])
        .map((label) => label.textContent)
        .find((text) => text?.trim());
    const wrapperLabel = field.closest('.form-row')?.querySelector('label')?.textContent;
    return (associatedLabel || wrapperLabel || '')
        .replace(/\s+/g, ' ')
        .replace(/\s*\*+\s*$/, '')
        .trim();
}

/**
 * Determine whether a checkout field belongs to the shipping address.
 *
 * @param field - The checkout field to inspect.
 * @returns Whether its ID or name has the shipping prefix.
 */
function isShippingField(field: CheckoutField): boolean {
    return field.id.startsWith('shipping_') || field.name.startsWith('shipping_');
}

/**
 * Validate eligible checkout fields, excluding shipping fields when shipping to another address is off.
 *
 * @param form - The checkout form containing the fields to validate.
 * @returns Whether all eligible fields pass validation.
 */
function validateFields(form: HTMLFormElement): boolean {
    let valid = true;
    const requiredFields = new Set<CheckoutField>();
    const nativeConstraintFields = new Set<CheckoutField>();
    const shipToDifferentAddress = form.querySelector<HTMLInputElement>('#ship-to-different-address-checkbox');
    const validateShippingFields = !shipToDifferentAddress || shipToDifferentAddress.checked;

    /**
     * Check whether a field should participate in this form validation pass.
     *
     * @param field - The element to check.
     * @returns Whether it is eligible and enabled for this validation pass.
     */
    const shouldValidateField = (field: Element): field is CheckoutField => {
        if (!isEligibleField(field)) return false;
        if (!validateShippingFields && isShippingField(field)) {
            clearError(field.id || field.name || '');
            return false;
        }
        return true;
    };

    form.querySelectorAll('.validate-required').forEach((wrapper) => {
        wrapper.querySelectorAll(REQUIRED_FIELD_SELECTOR).forEach((field) => {
            if (shouldValidateField(field)) requiredFields.add(field);
        });
    });
    form.querySelectorAll<CheckoutField>(REQUIRED_FIELD_SELECTOR).forEach((field) => {
        if (!shouldValidateField(field) || !field.willValidate) return;
        nativeConstraintFields.add(field);
        if (field.required) requiredFields.add(field);
    });

    new Set([...requiredFields, ...nativeConstraintFields]).forEach((field) => {
        if (!validateField(field, form, requiredFields.has(field))) valid = false;
    });

    return valid;
}

/**
 * Validate a field's required value, native constraints, and configured Brazilian format.
 *
 * @param field - The checkout field to validate.
 * @param form - The checkout form containing the field.
 * @param required - Whether the field must have a value.
 * @returns Whether the field is valid.
 */
function validateField(field: CheckoutField, form: HTMLFormElement, required: boolean): boolean {
    const fieldId = field.id || field.name || '';
    if (!fieldId) return true;

    if (required && !fieldHasValue(field, form)) {
        showError('required', fieldId, fieldLabel(field));
        return false;
    }

    if (!field.validity.valid) {
        showError('invalid', fieldId, fieldLabel(field));
        return false;
    }

    const brazilianFieldConfig = brazilianConfigFields.find((config) => config.id === fieldId);
    if (brazilianFieldConfig) {
        const personType = document.getElementById('billing_persontype');
        const personTypeValue = isEligibleField(personType) ? personType.value : '';
        if (personTypeValue && !validateBrazilianField(field, brazilianFieldConfig, personTypeValue)) {
            showError('invalid', fieldId, fieldLabel(field));
            return false;
        }
    }

    clearError(fieldId);
    return true;
}

/**
 * Validate a configured Brazilian field according to the selected person type.
 *
 * @param field - The checkout field to validate.
 * @param brazilianFieldConfig - The field-specific Brazilian validation configuration.
 * @param personType - The selected person type value.
 * @returns Whether the field is valid for the selected person type.
 */
function validateBrazilianField(field: CheckoutField, brazilianFieldConfig: BrazilianFieldConfig, personType: string): boolean {
    if (
        ('billing_cpf' === field.id && personType !== '1') ||
        ('billing_cnpj' === field.id && personType !== '2')
    ) {
        clearError(field.id);
        return true;
    }

    const value = field.value.trim();

    if (!value) {
        clearError(field.id);
        return true;
    }

    if (brazilianFieldConfig.validate && brazilianFieldConfig.validate(value)) {
        clearError(field.id);
        return true;
    }

    return false;
}

/**
 * Focus the field associated with the first displayed validation error.
 *
 * @param form - The checkout form containing the invalid field.
 * @returns Nothing.
 */
function focusFirstInvalidField(form: HTMLFormElement): void {
    const invalidError = Array.from(
        form.querySelectorAll<HTMLSpanElement>('span[data-error-for]')
    ).find((error) => error.textContent?.trim());
    if (!invalidError?.dataset.errorFor) return;

    const field = document.getElementById(invalidError.dataset.errorFor);
    if (field instanceof HTMLElement && form.contains(field)) field.focus();
}

/**
 * Attach change and blur validation handlers to a checkout form.
 *
 * @param form - The checkout form receiving the handlers.
 * @returns Nothing.
 */
function attachFieldHandlers(form: HTMLFormElement): void {
    /**
     * Validate a required field when it changes or loses focus.
     *
     * @param event - The change or blur event.
     * @returns Nothing.
     */
    const validate = (event: Event) => {
        if (!(event.target instanceof Element)) return;

        const field = event.target.closest(REQUIRED_FIELD_SELECTOR);
        if (!isEligibleField(field) || !form.contains(field)) return;

        const required = field.required || field.closest('.validate-required') !== null;
        if (!required) return;

        validateField(field, form, true);
    };

    form.addEventListener('change', validate);
    form.addEventListener('blur', validate, true);
}

/**
 * Attach capitalization handlers to name and address text fields.
 *
 * @param form - The checkout form receiving the handlers.
 * @returns Nothing.
 */
function attachCapitalizationHandlers(form: HTMLFormElement): void {
    const fields = form.querySelectorAll<HTMLInputElement | HTMLTextAreaElement>(
        'input[id$="_first_name"], input[id$="_last_name"], input[id$="_address_1"], input[id$="_address_2"], input[id$="_city"], input[id$="_neighborhood"]'
    );

    fields.forEach((field) => {
        /**
         * Normalize and capitalize the current field value.
         *
         * @returns Nothing.
         */
        const capitalize = () => {
            field.value = capitalizeWords(field.value);
        };
        field.addEventListener('change', capitalize);
        field.addEventListener('blur', capitalize);
    });
}

/**
 * Initialize checkout submission validation and field event handlers.
 *
 * @returns Nothing.
 */
export function initCheckoutValidation(): void {
    const form = document.querySelector<HTMLFormElement>('form.checkout, form.woocommerce-checkout');
    if (!form) return;

    form.noValidate = true;

    /**
     * Stop submission and focus the first invalid field when validation fails.
     *
     * @param event - The checkout form submission event.
     * @returns Nothing.
     */
    const handleSubmit = (event: SubmitEvent): void => {
        if (validateFields(form)) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        focusFirstInvalidField(form);
    };
    form.addEventListener('submit', handleSubmit, true);

    attachFieldHandlers(form);
    attachCapitalizationHandlers(form);
}
