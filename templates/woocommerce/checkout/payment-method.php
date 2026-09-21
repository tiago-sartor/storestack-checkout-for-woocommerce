<?php

/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<li class="wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?> transition-colors has-[:checked]:border-2 has-[:checked]:border-neutral-800 has-[:checked]:bg-olive-100 has-[:checked]:first:rounded-t-xl has-[:checked]:last:rounded-b-xl">

    <label class="flex items-center justify-between p-4 sm:px-5 cursor-pointer select-none" for="payment_method_<?php echo esc_attr($gateway->id); ?>">
        <div class="flex items-center gap-3.5 min-w-0">
            <input
                class="input-radio size-4.5 cursor-pointer accent-neutral-900 shrink-0"
                id="payment_method_<?php echo esc_attr($gateway->id); ?>"
                type="radio"
                name="payment_method"
                value="<?php echo esc_attr($gateway->id); ?>"
                <?php checked($gateway->chosen, true); ?>
                data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />
            <span class="text-sm font-medium text-neutral-900 truncate"><?php echo esc_html($gateway->get_title()); ?></span>
        </div>
        <div class="payment-method-icon shrink-0 flex items-center gap-2 [&_img]:h-6 [&_img]:w-auto [&_img]:object-contain">
            <?php echo wp_kses_post($gateway->get_icon()); ?>
        </div>
    </label>

    <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
        <div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?> px-4 pb-4 sm:px-5 sm:pb-5 text-sm text-neutral-600 leading-relaxed" <?php if (!$gateway->chosen) echo 'style="display:none;"'; ?>>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>

</li>