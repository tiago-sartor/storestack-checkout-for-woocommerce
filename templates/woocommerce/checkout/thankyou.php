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

<div class="woocommerce-order py-8 sm:py-12">

    <?php
    if ($order) :

        do_action('woocommerce_before_thankyou', $order->get_id());
    ?>

        <?php if ($order->has_status('failed')) : ?>

            <div class="p-6 rounded-2xl border border-red-200 bg-red-50/70 max-w-2xl mx-auto text-center space-y-4">
                <div class="size-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-red-900">
                    <?php esc_html_e('Order Failed', 'woocommerce'); ?>
                </h2>

                <p class="text-sm text-red-700 leading-relaxed">
                    <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
                </p>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="w-full sm:w-auto px-6 py-3 rounded-lg font-semibold text-sm text-white bg-red-700 hover:bg-red-800 transition-colors">
                        <?php esc_html_e('Pay', 'woocommerce'); ?>
                    </a>
                    <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="w-full sm:w-auto px-6 py-3 rounded-lg font-medium text-sm text-neutral-700 bg-white border border-neutral-300 hover:bg-neutral-50 transition-colors">
                            <?php esc_html_e('My account', 'woocommerce'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
            <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

        <?php else : ?>

            <div class="relative grid grid-cols-1 lg:grid-cols-12 lg:items-start lg:gap-10 xl:gap-14">

                <!-- Left Column: Confirmation & Customer Details -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Header with Checkmark -->
                    <div class="flex items-start gap-4">
                        <div class="size-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                                <?php printf(esc_html__('Order #%s', 'woocommerce'), esc_html($order->get_order_number())); ?>
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight mt-0.5">
                                <?php printf(esc_html__('Thank you, %s!', 'woocommerce'), esc_html($order->get_billing_first_name())); ?>
                            </h1>
                        </div>
                    </div>

                    <!-- Order Confirmation Message Card -->
                    <div class="p-5 rounded-xl border border-neutral-200/90 bg-white shadow-2xs space-y-1">
                        <h3 class="text-sm font-semibold text-neutral-900">
                            <?php echo esc_html__('Your order is confirmed', 'woocommerce'); ?>
                        </h3>
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            <?php printf(esc_html__('We\'ve accepted your order, and we\'ll send updates to %s.', 'woocommerce'), '<strong class="font-medium text-neutral-900">' . esc_html($order->get_billing_email()) . '</strong>'); ?>
                        </p>
                    </div>

                    <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>

                    <?php
                    remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
                    do_action('woocommerce_thankyou', $order->get_id());
                    ?>

                    <?php if ($order->get_user_id() === get_current_user_id()) : ?>
                        <!-- Customer Information Card (Grid) -->
                        <div class="p-6 rounded-xl border border-neutral-200/90 bg-white shadow-2xs space-y-6">
                            <h3 class="text-base font-semibold text-neutral-900">
                                <?php echo esc_html__('Order details', 'woocommerce'); ?>
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs sm:text-sm">
                                <!-- Contact Info -->
                                <div>
                                    <h4 class="font-medium text-neutral-500 mb-1.5 uppercase text-2xs tracking-wider">
                                        <?php echo esc_html__('Contact information', 'woocommerce'); ?>
                                    </h4>
                                    <p class="text-neutral-900"><?php echo esc_html($order->get_billing_email()); ?></p>
                                    <?php if ($phone = $order->get_billing_phone()) : ?>
                                        <p class="text-neutral-600 mt-0.5"><?php echo esc_html($phone); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <h4 class="font-medium text-neutral-500 mb-1.5 uppercase text-2xs tracking-wider">
                                        <?php echo esc_html__('Payment method', 'woocommerce'); ?>
                                    </h4>
                                    <p class="text-neutral-900 font-medium"><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                                </div>

                                <!-- Shipping Address -->
                                <div>
                                    <h4 class="font-medium text-neutral-500 mb-1.5 uppercase text-2xs tracking-wider">
                                        <?php echo esc_html__('Shipping address', 'woocommerce'); ?>
                                    </h4>
                                    <address class="not-italic text-neutral-800 leading-relaxed">
                                        <?php
                                        $show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
                                        if ($show_shipping) :
                                            echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce')));
                                            do_action('woocommerce_order_details_after_customer_address', 'shipping', $order);
                                        else :
                                            echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce')));
                                            do_action('woocommerce_order_details_after_customer_address', 'billing', $order);
                                        endif;
                                        ?>
                                    </address>
                                </div>

                                <!-- Billing Address -->
                                <div>
                                    <h4 class="font-medium text-neutral-500 mb-1.5 uppercase text-2xs tracking-wider">
                                        <?php echo esc_html__('Billing address', 'woocommerce'); ?>
                                    </h4>
                                    <address class="not-italic text-neutral-800 leading-relaxed">
                                        <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
                                    </address>
                                </div>

                                <?php if ($customer_note = $order->get_customer_note()) : ?>
                                    <div class="sm:col-span-2 pt-2 border-t border-neutral-100">
                                        <h4 class="font-medium text-neutral-500 mb-1 uppercase text-2xs tracking-wider">
                                            <?php echo esc_html__('Order notes', 'woocommerce'); ?>
                                        </h4>
                                        <p class="text-neutral-800 italic"><?php echo wp_kses_post($customer_note); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                        <a
                            class="text-sm font-medium text-neutral-600 hover:text-neutral-900 flex items-center gap-1.5 transition-colors"
                            href="<?php echo esc_url(home_url()); ?>"
                            role="button">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                            <span><?php echo esc_html__('Continue shopping', 'woocommerce'); ?></span>
                        </a>

                        <?php if (is_user_logged_in()) : ?>
                            <a
                                class="w-full sm:w-auto px-6 py-3.5 rounded-lg text-sm font-semibold text-white bg-neutral-900 hover:bg-neutral-800 active:bg-black transition-colors shadow-sm text-center"
                                href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                                role="button">
                                <?php echo esc_html__('View account', 'woocommerce'); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Right Column: Order Details / Items Summary -->
                <div class="lg:col-span-5 mt-8 lg:mt-0">
                    <div class="lg:sticky lg:top-8 bg-neutral-50 border border-neutral-200/90 rounded-2xl p-5 sm:p-7 shadow-xs">
                        <?php wc_get_template('order/order-details.php', ['order' => $order]); ?>
                    </div>
                </div>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>