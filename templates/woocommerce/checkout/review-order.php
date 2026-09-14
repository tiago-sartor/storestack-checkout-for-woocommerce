<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<div class="shop_table woocommerce-checkout-review-order-table overflow-clip border border-neutral-300 divide-y divide-neutral-300 rounded-md">

    <ul role="list" class="pt-3 px-4">

        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                $product_thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                $product_quantity = apply_filters('woocommerce_checkout_cart_item_quantity', $cart_item['quantity'], $cart_item, $cart_item_key);
                $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                $product_attributes = $cart_item['variation'] ?? [];
                $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
        ?>
                <li class="cart_item flex py-4 text-sm">

                    <div class="relative size-20 shrink-0">
                        <div class="overflow-clip rounded-sm">
                            <?php echo wp_kses_post($product_thumbnail); ?>
                        </div>
                        <span class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 z-10 flex items-center justify-center min-w-5.5 min-h-5.5 px-1 text-xs font-medium text-white rounded-full bg-neutral-900"><?php echo esc_html($product_quantity); ?></span>
                    </div>

                    <div class="ml-6 flex flex-1 flex-row flex-nowrap items-start justify-between gap-4">

                        <div class="flex flex-col">

                            <h3 class="font-medium">
                                <?php echo wp_kses_post($product_name); ?>
                            </h3>

                            <p class="price order-review"><?php echo wp_kses_post($product_price); ?></p>

                            <?php foreach ($product_attributes as $attribute_name => $attribute_value) :
                                $attribute_name = str_replace('attribute_', '', $attribute_name);
                                $term = get_term_by('slug', $attribute_value, $attribute_name);
                            ?>
                                <p class="mt-1 text-xs">
                                    <span class="font-semibold"><?php echo esc_html(wc_attribute_label($attribute_name, $_product)); ?>:</span>
                                    <span><?php echo esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute_name, $_product)); ?></span>
                                </p>
                            <?php endforeach; ?>

                        </div>

                        <p class="font-medium whitespace-nowrap"><?php echo wp_kses_post($product_subtotal); ?></p>

                    </div>
                </li>
        <?php
            }
        }
        ?>

    </ul>

    <?php if (wc_coupons_enabled()) : ?>
        <div
            x-data="{open: false, active: false}"
            @click.outside="open = false"
            class="coupon p-4">

            <div @click="open = !open" class="relative flex items-center justify-between cursor-pointer">
                <span>Adicionar um cupom</span>
                <span :class="open ? 'rotate-180' : ''" class="transition-transform duration-200">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </span>
                <span class="absolute -inset-2"></span>
            </div>

            <div x-cloak x-transition x-show="open" class="flex gap-2 mt-4">
                <div class="relative overflow-hidden flex flex-1 items-center justify-start h-13 px-3 py-1 border border-neutral-500 rounded-sm bg-white">
                    <label for="coupon_code" :class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-400'" class="absolute pointer-events-none transition-all ease-in-out duration-200">Digite o código</label>
                    <input x-ref="coupon" @keydown.enter.prevent="applyCoupon($el.value)" @focus="active = true" @blur="if ($el.value === '') active = false" type="text" name="coupon_code" :class="active ? 'pt-4.5' : ''" class="size-full focus:outline-none" id="coupon_code" value="">
                </div>
                <button @click.prevent="applyCoupon($refs.coupon.value)" type="submit" class="flex items-center justify-center px-6 text-sm border border-neutral-500 rounded-sm hover:bg-neutral-50" name="apply_coupon" value="<?php esc_attr_e('Apply', 'woocommerce'); ?>"><?php esc_html_e('Apply', 'woocommerce'); ?></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="p-4 space-y-4">

        <div class="cart-subtotal flex justify-between">
            <span class="text-left"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
            <span class="text-right font-semibold whitespace-nowrap"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php if ($coupons = WC()->cart->get_coupons()) : ?>
            <div class="cart-coupons flex justify-between">
                <span class="text-left "><?php echo (count($coupons) > 1 ? 'Cupons' : 'Cupom'); ?></span>
                <div class="flex flex-wrap justify-end gap-2">
                    <?php foreach ($coupons as $code => $coupon) : ?>
                        <span class="relative inline-flex flex-nowrap items-center justify-center gap-0.5 px-2 py-1 text-xs font-medium text-neutral-800 border border-neutral-300 rounded-sm bg-neutral-50">
                            <?php echo esc_html(strtoupper($code)); ?>
                            <a
                                href="<?php echo esc_url(add_query_arg('remove_coupon', rawurlencode($code), wc_get_checkout_url())); ?>"
                                class="woocommerce-remove-coupon group relative -mr-1 size-4 rounded-xs hover:bg-neutral-500/20"
                                data-coupon="<?php echo esc_attr($code); ?>"
                                role="button"
                                aria-label="<?php esc_attr_e('Remove', 'woocommerce'); ?>">
                                <svg viewBox="0 0 14 14" class="size-4 stroke-neutral-600/50 group-hover:stroke-neutral-600/75">
                                    <path d="M4 4l6 6m0-6l-6 6"></path>
                                </svg>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (($discount_total = WC()->cart->get_discount_total()) && $discount_total > 0) : ?>
            <div class="cart-discount flex justify-between">
                <span class="text-left"><?php echo 'Desconto'; ?></span>
                <span class="font-semibold text-green-600 text-right whitespace-nowrap"><?php echo wp_kses_post('-' . wc_price($discount_total)); ?></span>
            </div>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="fee flex justify-between">
                <span class="text-left"><?php echo esc_html($fee->name); ?></span>
                <span class="text-right font-semibold text-green-600 whitespace-nowrap"><?php wc_cart_totals_fee_html($fee); ?></span>
            </div>
        <?php endforeach; ?>

    </div>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
        <?php wc_cart_totals_shipping_html(); ?>
    <?php endif; ?>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <div class="order-total flex justify-between p-4 text-xl font-semibold">
        <span class="text-left"><?php esc_html_e('Total', 'woocommerce'); ?></span>
        <span x-html="total = '<?php echo esc_html(WC()->cart->get_total()); ?>'" class="text-right whitespace-nowrap"><?php echo wp_kses_post(WC()->cart->get_total()); ?></span>
    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>

</div>