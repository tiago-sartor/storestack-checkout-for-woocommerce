/**
 * brazilian-fields-validation.ts
 *
 * Only enqueued when the "Brazilian Market on WooCommerce"
 * (Extra_Checkout_Fields_For_Brazil) plugin is active.
 *
 * Validates: CPF, CNPJ, phone, postcode (CEP), email.
 * Formats:   text fields on blur (title-case).
 * Renders:   error messages into <span class="field-error" data-error-for="fieldId">.
 */

import {
    isValidEmail,
    isValidCPF,
    isValidCNPJ,
    isValidBRPhone,
    isValidBRPostcode,
    capitalizeWords,
    showFieldError,
    clearFieldError,
} from './validation';

// ─── Validation messages ───────────────────────────────────────────────────────

const i18n = {
    required: (label: string) => `${label} é obrigatório.`,
    invalidEmail: 'Por favor, insira um endereço de e-mail válido.',
    invalidCPF: 'CPF inválido.',
    invalidCNPJ: 'CNPJ inválido.',
    invalidPhone: 'Telefone inválido.',
    invalidPostcode: 'CEP inválido.',
};

// ─── Field configuration ───────────────────────────────────────────────────────

interface FieldConfig {
    id: string;
    label: string;
    required: boolean;
    validate?: (value: string) => string | null; // null = valid
    format?: (value: string) => string;
}

const FIELD_CONFIGS: FieldConfig[] = [
    {
        id: 'billing_email',
        label: 'E-mail',
        required: true,
        validate: (v) => (!isValidEmail(v) ? i18n.invalidEmail : null),
    },
    {
        id: 'billing_cpf',
        label: 'CPF',
        required: true,
        validate: (v) => (!isValidCPF(v) ? i18n.invalidCPF : null),
    },
    {
        id: 'billing_cnpj',
        label: 'CNPJ',
        required: true,
        validate: (v) => (!isValidCNPJ(v) ? i18n.invalidCNPJ : null),
    },
    {
        id: 'billing_phone',
        label: 'Telefone',
        required: true,
        validate: (v) => (!isValidBRPhone(v) ? i18n.invalidPhone : null),
    },
    {
        id: 'billing_postcode',
        label: 'CEP',
        required: true,
        validate: (v) => (!isValidBRPostcode(v) ? i18n.invalidPostcode : null),
    },
    {
        id: 'billing_first_name',
        label: 'Nome',
        required: true,
        format: capitalizeWords,
    },
    {
        id: 'billing_last_name',
        label: 'Sobrenome',
        required: true,
        format: capitalizeWords,
    },
    {
        id: 'billing_address_1',
        label: 'Endereço',
        required: true,
        format: capitalizeWords,
    },
    {
        id: 'billing_city',
        label: 'Cidade',
        required: true,
        format: capitalizeWords,
    },
    {
        id: 'billing_neighborhood',
        label: 'Bairro',
        required: true,
        format: capitalizeWords,
    },
];

// ─── Core logic ────────────────────────────────────────────────────────────────

function validateField(config: FieldConfig): void {
    const input = document.getElementById(config.id) as HTMLInputElement | null;
    if (!input) return;

    const value = input.value.trim();

    // Required check.
    if (config.required && !value) {
        showFieldError(config.id, i18n.required(config.label));
        return;
    }

    // Pattern validation.
    if (value && config.validate) {
        const error = config.validate(value);
        if (error) {
            showFieldError(config.id, error);
            return;
        }
    }

    clearFieldError(config.id);
}

function attachBlurValidation(config: FieldConfig): void {
    const input = document.getElementById(config.id) as HTMLInputElement | null;
    if (!input) return;

    input.addEventListener('blur', () => validateField(config));

    // Also re-validate on every input keystroke so errors clear as user types.
    input.addEventListener('input', () => {
        if (input.value.trim()) clearFieldError(config.id);
    });
}

/**
 * Entry point called automatically on DOMContentLoaded.
 */
function init(): void {
    FIELD_CONFIGS.forEach(attachBlurValidation);
}

document.addEventListener('DOMContentLoaded', init);

