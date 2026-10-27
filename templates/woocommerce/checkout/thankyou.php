<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<div class="woocommerce-order mx-auto max-w-6xl p-4 lg:p-10">

    <?php
    if ($order) :

        do_action('woocommerce_before_thankyou', $order->get_id());
    ?>
        <div class="relative grid grid-cols-1 lg:grid-cols-5 lg:items-start lg:gap-12 xl:gap-18">

            <!-- Left Column: Confirmation & Customer Details -->
            <div class="lg:col-span-3 space-y-6">

                <?php if ($order->has_status('failed')) : ?>

                    <?php
                    $default_order_failed_text = esc_html__('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce');

                    /**
                     * Filters the message shown when an order has failed.
                     *
                     * @param string   $message The failed order message.
                     * @param WC_Order $order   The failed order.
                     *
                     * @since 11.2.0
                     */
                    $order_failed_text = apply_filters('woocommerce_thankyou_order_failed_text', $default_order_failed_text, $order);
                    $order_failed_text = is_string($order_failed_text) ? $order_failed_text : $default_order_failed_text;
                    ?>

                    <div class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-red-300 bg-red-50 p-6 text-center">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-full border-2 border-red-700 bg-red-100 text-red-700">
                            <svg class="size-6.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl text-red-800 font-bold">
                                <?php esc_html_e('Order Failed', 'woocommerce'); ?>
                            </h2>
                            <span class="text-xs text-red-700 font-bold tracking-wider uppercase">
                                <?php /* translators: %s: Order number. */ ?>
                                <?php printf(esc_html__('Order #%s', 'woocommerce'), esc_html($order->get_order_number())); ?>
                            </span>
                        </div>

                        <p class="text-sm text-red-700 leading-relaxed"><?php echo wp_kses_post($order_failed_text); ?></p>

                        <div class="flex w-full flex-col items-center justify-center gap-3 pt-2 sm:flex-row">
                            <a
                                class="w-full rounded-lg bg-red-800 px-6 py-3 text-sm font-semibold text-white! sm:w-auto"
                                href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
                                role="button">
                                <?php esc_html_e('Pay', 'woocommerce'); ?>
                            </a>
                            <?php if (is_user_logged_in()) : ?>
                                <a
                                class="w-full rounded-lg border border-neutral-300 bg-white px-6 py-3 text-sm font-medium sm:w-auto"
                                href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                                role="button">
                                    <?php esc_html_e('My account', 'woocommerce'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else : ?>

                    <!-- Header with Checkmark -->
                    <div class="thankyou-header flex items-center gap-4">
                        <div class="thankyou-header__checkmark flex size-12 shrink-0 items-center justify-center rounded-full border-2 border-emerald-600 bg-emerald-50 text-emerald-600">
                            <svg class="size-6.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <span class="thankyou-header__order# text-xs font-medium tracking-wide uppercase">
                                <?php /* translators: %s: Order number. */ ?>
                                <?php printf(esc_html__('Order #%s', 'woocommerce'), esc_html($order->get_order_number())); ?>
                            </span>
                            <h1 class="thankyou-header__thankyou text-2xl sm:text-3xl font-bold tracking-tight mt-0.5">
                                <?php /* translators: %s: Customer first name. */ ?>
                                <?php printf(esc_html__('Thank you, %s!', 'woocommerce'), esc_html($order->get_billing_first_name())); ?>
                            </h1>
                        </div>
                    </div>

                <?php endif; ?>

                <?php do_action('storestack_checkout_before_payment_info') ?>

                <div class="text-sm space-y-4">
                    <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
                </div>

                <?php do_action('storestack_checkout_after_payment_info') ?>

                <?php
                // We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
                if ($order->get_user_id() === get_current_user_id()) :
                ?>
                    <div class="order-info space-y-4">
                        <h2 class="text-lg font-semibold">
                            <?php echo esc_html__('Order Info', 'woocommerce'); ?>
                        </h2>
                        <p class="order-info-message text-sm"><?php esc_html_e('Review the information below carefully. If you need to correct any details, please contact us as soon as possible.', 'storestack-checkout-for-woocommerce'); ?></p>

                        <?php do_action('storestack_checkout_before_order_info') ?>

                        <div class="order-info-table overflow-hidden rounded-lg border border-neutral-300">
                            <table class="w-full table-auto text-sm">
                                <tbody class="divide-y divide-neutral-300">
                                    <tr>
                                        <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap"><?php esc_html_e('E-mail', 'storestack-checkout-for-woocommerce'); ?></th>
                                        <td class="break-all p-4 align-top"><?php echo esc_html($order->get_billing_email()); ?></td>
                                    </tr>
                                    <?php
                                    $billing_cellphone = $order->get_meta('_billing_cellphone', true);
                                    if ($billing_cellphone) :
                                    ?>
                                        <tr>
                                            <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap"><?php esc_html_e('Mobile Phone', 'storestack-checkout-for-woocommerce'); ?></th>
                                            <td class="p-4 align-top"><?php echo esc_html($billing_cellphone); ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap"><?php esc_html_e('Phone', 'storestack-checkout-for-woocommerce'); ?></th>
                                            <td class="p-4 align-top"><?php echo esc_html($order->get_billing_phone()); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap">
                                            <?php
                                            $show_shipping = wc_shipping_enabled() && $order->needs_shipping_address();
                                            if ($show_shipping) :
                                                esc_html_e('Shipping', 'woocommerce');
                                            else :
                                                esc_html_e('Billing', 'woocommerce');
                                            endif;
                                            ?>
                                        </th>
                                        <td class="p-4 align-top">
                                            <address class="not-italic!">
                                                <?php
                                                $show_shipping_address = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
                                                if ($show_shipping_address) :
                                                    echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce')));
                                                    do_action('woocommerce_order_details_after_customer_address', 'shipping', $order);
                                                else :
                                                    echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce')));
                                                    do_action('woocommerce_order_details_after_customer_address', 'billing', $order);
                                                endif;
                                                ?>
                                            </address>
                                        </td>
                                    </tr>
                                    <?php
                                    $customer_note = $order->get_customer_note();
                                    if ($customer_note) :
                                    ?>
                                        <tr>
                                            <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap"><?php esc_html_e('Additional Info', 'storestack-checkout-for-woocommerce'); ?></th>
                                            <td class="p-4 align-top"><?php echo wp_kses_post($customer_note); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th scope="row" class="w-px p-4 text-left align-top font-medium whitespace-nowrap"><?php esc_html_e('Payment', 'storestack-checkout-for-woocommerce'); ?></th>
                                        <td class="p-4 align-top"><?php echo wp_kses_post($order->get_payment_method_title()); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php do_action('storestack_checkout_after_order_info') ?>

                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse items-center justify-between gap-4 pt-4 sm:flex-row">
                    <a
                        class="mr-1.5 flex w-full items-center justify-center gap-1.5 py-2.5 text-center text-sm font-medium text-neutral-600 hover:text-neutral-900 sm:w-auto sm:justify-start sm:text-left"
                        href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                        role="button">
                        <svg class="size-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                        <span><?php echo esc_html__('Return to shop', 'woocommerce'); ?></span>
                    </a>

                    <?php if (is_user_logged_in()) : ?>
                        <a
                            class="w-full rounded-lg bg-neutral-900 px-6 py-3.5 text-center text-sm font-semibold text-white! shadow-sm hover:bg-neutral-800 active:bg-black sm:w-auto"
                            href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                            role="button">
                            <?php echo esc_html__('My account', 'woocommerce'); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Column: Order Details / Items Summary -->
            <div class="lg:col-span-2 mt-8 lg:mt-0">
                <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
            </div>

        </div>

    <?php endif; ?>

</div>