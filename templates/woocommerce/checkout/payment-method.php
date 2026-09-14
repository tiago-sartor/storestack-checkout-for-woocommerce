<?php

/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<li class="rounded-md border border-neutral-300 bg-white p-4 has-[input[checked]]:border has-[input[checked]]:border-gold-800 has-[input[checked]]:bg-gold-100 wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?>">

    <label class="flex items-center justify-start gap-4 cursor-pointer" for="payment_method_<?php echo esc_attr($gateway->id); ?>">
        <input class="input-radio size-5 cursor-pointer" id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" name="payment_method" value="<?php echo esc_attr($gateway->id); ?>" <?php checked($gateway->chosen, true); ?> data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />
        <div class="flex flex-1 items-center justify-between">
            <span class="text-left font-semibold tracking-wide uppercase"><?php echo esc_html($gateway->get_title()); ?></span>
            <?php echo wp_kses_post($gateway->get_icon()); ?>
        </div>
    </label>

    <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
        <div class="mt-4 text-sm payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) echo 'style="display:none;"'; ?>>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>

</li>