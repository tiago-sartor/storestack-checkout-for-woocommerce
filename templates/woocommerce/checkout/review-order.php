<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<div class="shop_table woocommerce-checkout-review-order-table">

    <!-- Product Line Items -->
    <ul role="list" class="pb-4">

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
                <li class="cart_item flex items-center py-3.5 text-sm gap-5">

                    <!-- Thumbnail with Quantity Badge -->
                    <div class="relative size-16 lg:size-20 shrink-0 rounded-lg border border-neutral-200 bg-white p-0.5 flex items-center justify-center">
                        <div class="size-full overflow-clip rounded-md flex items-center justify-center [&>img]:size-full [&>img]:object-cover">
                            <?php echo wp_kses_post($product_thumbnail); ?>
                        </div>
                        <span class="absolute -top-2.5 -right-2.5 z-10 flex items-center justify-center size-5.5 px-1 text-xs font-semibold text-white rounded-full bg-neutral-800">
                            <?php echo esc_html($product_quantity); ?>
                        </span>
                    </div>

                    <!-- Details & Price -->
                    <div class="flex flex-1 items-start justify-between gap-4">
                        <div class="flex flex-col">
                            <h4 class="font-medium text-neutral-900 truncate">
                                <?php echo wp_kses_post($product_name); ?>
                            </h4>

                            <?php if (!empty($product_attributes)) : ?>
                                <div class="mt-0.5 space-y-0.5">
                                    <?php foreach ($product_attributes as $attribute_name => $attribute_value) :
                                        $attribute_name = str_replace('attribute_', '', $attribute_name);
                                        $term = get_term_by('slug', $attribute_value, $attribute_name);
                                        $attr_label = wc_attribute_label($attribute_name, $_product);
                                        $attr_val = $term ? apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute_name, $_product) : $attribute_value;
                                    ?>
                                        <p class="text-xs">
                                            <span class="text-neutral-500"><?php echo esc_html($attr_label); ?>:</span>
                                            <span class="font-medium text-neutral-700"><?php echo esc_html($attr_val); ?></span>
                                        </p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <span class="font-medium text-neutral-900 whitespace-nowrap shrink-0">
                            <?php echo wp_kses_post($product_subtotal); ?>
                        </span>
                    </div>
                </li>
        <?php
            }
        }
        ?>

    </ul>

    <!-- Coupon Input (always visible) -->
    <?php if (wc_coupons_enabled()) : ?>
        <div class="py-4">
            <div class="flex gap-2.5">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="coupon_code"
                        id="coupon_code"
                        placeholder="<?php esc_attr_e('Discount code or coupon', 'woocommerce'); ?>"
                        class="w-full h-11 px-3.5 text-sm bg-white border border-neutral-200 rounded-lg placeholder-neutral-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 transition-colors"
                        value="" />
                </div>
                <button
                    id="apply_coupon_btn"
                    type="button"
                    class="h-11 px-5 text-sm font-medium text-neutral-700 bg-neutral-200 hover:bg-neutral-300 active:bg-neutral-400 rounded-lg transition-colors cursor-pointer shrink-0"
                    name="apply_coupon"
                    value="<?php esc_attr_e('Apply', 'woocommerce'); ?>">
                    <?php esc_html_e('Apply', 'woocommerce'); ?>
                </button>
            </div>

            <!-- Applied Coupon Tags -->
            <?php if ($coupons = WC()->cart->get_coupons()) : ?>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php foreach ($coupons as $code => $coupon) : ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold tracking-wide uppercase bg-neutral-200 text-neutral-800 rounded-md">
                            <svg class="size-3.5 text-neutral-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            <?php echo esc_html(strtoupper($code)); ?>
                            <a
                                href="<?php echo esc_url(add_query_arg('remove_coupon', rawurlencode($code), wc_get_checkout_url())); ?>"
                                class="woocommerce-remove-coupon group relative size-4 rounded-xs hover:bg-neutral-300 flex items-center justify-center transition-colors"
                                data-coupon="<?php echo esc_attr($code); ?>"
                                role="button"
                                aria-label="<?php esc_attr_e('Remove', 'woocommerce'); ?>">
                                <svg viewBox="0 0 14 14" class="size-3 stroke-neutral-600 group-hover:stroke-neutral-900">
                                    <path d="M4 4l6 6m0-6l-6 6" stroke-width="1.5"></path>
                                </svg>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Order Totals Breakdown -->
    <div class="py-4 space-y-3 text-sm">

        <div class="cart-subtotal flex justify-between items-center text-neutral-600">
            <span><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
            <span class="font-medium text-neutral-900 whitespace-nowrap"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php if (($discount_total = WC()->cart->get_discount_total()) && $discount_total > 0) : ?>
            <div class="cart-discount flex justify-between items-center text-neutral-600">
                <span><?php echo esc_html__('Discount', 'woocommerce'); ?></span>
                <span class="font-semibold text-emerald-600 whitespace-nowrap"><?php echo wp_kses_post('-' . wc_price($discount_total)); ?></span>
            </div>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="fee flex justify-between items-center text-neutral-600">
                <span><?php echo esc_html($fee->name); ?></span>
                <span class="font-medium text-neutral-900 whitespace-nowrap"><?php wc_cart_totals_fee_html($fee); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
            <div class="shipping-totals pt-1">
                <?php wc_cart_totals_shipping_html(); ?>
            </div>
        <?php endif; ?>

    </div>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <!-- Total Line -->
    <div class="order-total flex items-baseline justify-between pt-4">
        <div class="flex flex-col">
            <span class="text-base font-semibold text-neutral-900"><?php esc_html_e('Total', 'woocommerce'); ?></span>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider"><?php echo esc_html(get_woocommerce_currency()); ?></span>
            <span class="text-2xl font-bold text-neutral-900 tracking-tight whitespace-nowrap">
                <?php echo wp_kses_post(WC()->cart->get_total()); ?>
            </span>
        </div>
    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>

</div>