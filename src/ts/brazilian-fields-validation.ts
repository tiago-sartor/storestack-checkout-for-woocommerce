/**
 * brazilian-fields-validation.ts
 *
 * Only enqueued when the "Brazilian Market on WooCommerce"
 * (Extra_Checkout_Fields_For_Brazil) plugin is active.
 *
 * Validates: CPF, CNPJ, phone, postcode (CEP), email.
 * Formats:   text fields on blur (title-case).
 * Renders:   error messages into <p class="field-error" data-error-for="fieldId">.
 */

import {
    isValidEmail,
    isValidCPF,
    isValidCNPJ,
    isValidBRPhone,
    isValidBRPostcode,
    formatTextInput,
    showFieldError,
    clearFieldError,
} from './validation';

// ─── Formatting helpers ────────────────────────────────────────────────────────

function formatCPF(value: string): string {
    const d = value.replace(/\D/g, '').slice(0, 11);
    return d
        .replace(/^(\d{3})(\d)/, '$1.$2')
        .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)$/, '.$1-$2');
}

function formatCNPJ(value: string): string {
    const d = value.replace(/\D/g, '').slice(0, 14);
    return d
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)$/, '.$1/$2')
        .replace(/(\d{4})(\d)$/, '$1-$2');
}

function formatPhone(value: string): string {
    const d = value.replace(/\D/g, '').slice(0, 11);
    if (d.length <= 10) {
        return d.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    }
    return d.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
}

function formatPostcode(value: string): string {
    const d = value.replace(/\D/g, '').slice(0, 8);
    return d.replace(/^(\d{5})(\d)$/, '$1-$2');
}

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
        format: formatCPF,
    },
    {
        id: 'billing_cnpj',
        label: 'CNPJ',
        required: true,
        validate: (v) => (!isValidCNPJ(v) ? i18n.invalidCNPJ : null),
        format: formatCNPJ,
    },
    {
        id: 'billing_phone',
        label: 'Telefone',
        required: true,
        validate: (v) => (!isValidBRPhone(v) ? i18n.invalidPhone : null),
        format: formatPhone,
    },
    {
        id: 'billing_postcode',
        label: 'CEP',
        required: true,
        validate: (v) => (!isValidBRPostcode(v) ? i18n.invalidPostcode : null),
        format: formatPostcode,
    },
    {
        id: 'billing_first_name',
        label: 'Nome',
        required: true,
        format: formatTextInput,
    },
    {
        id: 'billing_last_name',
        label: 'Sobrenome',
        required: true,
        format: formatTextInput,
    },
    {
        id: 'billing_address_1',
        label: 'Endereço',
        required: true,
        format: formatTextInput,
    },
    {
        id: 'billing_city',
        label: 'Cidade',
        required: true,
        format: formatTextInput,
    },
    {
        id: 'billing_neighborhood',
        label: 'Bairro',
        required: true,
        format: formatTextInput,
    },
];

// ─── Core logic ────────────────────────────────────────────────────────────────

function validateAndFormatField(config: FieldConfig): void {
    const input = document.getElementById(config.id) as HTMLInputElement | null;
    if (!input) return;

    const value = input.value.trim();

    // Format on blur.
    if (config.format && value) {
        const formatted = config.format(value);
        if (formatted !== input.value) input.value = formatted;
    }

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

    input.addEventListener('blur', () => validateAndFormatField(config));

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

