<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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

<div class="woocommerce-order">

    <?php
    if ($order) :

        do_action('woocommerce_before_thankyou', $order->get_id());
    ?>

        <?php if ($order->has_status('failed')) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
                <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
            </p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
                <?php endif; ?>
            </p>

            <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
            <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

        <?php else : ?>

            <div class="relative lg:grid lg:grid-cols-[3fr_2fr] lg:items-start lg:gap-[10%]">

                <div>
                    <div class="flex items-center justify-start gap-3 mt-10 mb-8">
                        <span>
                            <svg class="size-16" viewBox="0 -960 960 960" fill="currentColor">
                                <path d="m422.46-323.69 255.85-255.85L650-607.85 422.46-380.31l-114-114L280.15-466l142.31 142.31ZM480.13-120q-74.67 0-140.41-28.34-65.73-28.34-114.36-76.92-48.63-48.58-76.99-114.26Q120-405.19 120-479.87q0-74.67 28.34-140.41 28.34-65.73 76.92-114.36 48.58-48.63 114.26-76.99Q405.19-840 479.87-840q74.67 0 140.41 28.34 65.73 28.34 114.36 76.92 48.63 48.58 76.99 114.26Q840-554.81 840-480.13q0 74.67-28.34 140.41-28.34 65.73-76.92 114.36-48.58 48.63-114.26 76.99Q554.81-120 480.13-120Zm-.13-40q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                            </svg>
                        </span>
                        <div class="flex flex-col items-start justify-center gap-1">
                            <span>Pedido nº <?php echo esc_html($order->get_order_number()); ?></span>
                            <h2 class="text-2xl font-bold">Obrigado, <?php echo esc_html($order->get_billing_first_name()); ?>!</h2>
                        </div>
                    </div>

                    <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>

                    <?php
                    remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
                    do_action('woocommerce_thankyou', $order->get_id());
                    ?>

                    <?php
                    // We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
                    if ($order->get_user_id() === get_current_user_id()) :
                    ?>
                        <div class="my-8 text-sm">
                            <p class="mb-4">Verifique as informações abaixo com atenção. Se precisar corrigir algum dado, por favor comunique-nos o mais breve possível.</p>
                            <div class="border border-neutral-300 divide-y divide-neutral-300 rounded-md">
                                <div class="flex items-start justify-start gap-6 p-4">
                                    <span class="min-w-20 font-medium">E-mail</span>
                                    <span><?php echo esc_html($order->get_billing_email()); ?></span>
                                </div>
                                <div class="flex items-start justify-start gap-6 p-4">
                                    <span class="min-w-20 font-medium">Celular</span>
                                    <span><?php echo esc_html($order->get_billing_phone()); ?></span>
                                </div>
                                <div class="flex items-start justify-start gap-6 p-4">
                                    <span class="min-w-20 font-medium">Entrega</span>
                                    <address class="not-italic!">
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
                                <?php if ($customer_note = $order->get_customer_note()) : ?>
                                    <div class="flex items-start justify-start gap-6 p-4">
                                        <span class="min-w-20 font-medium">Observação</span>
                                        <span><?php echo wp_kses_post($customer_note); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex items-start justify-start gap-6 p-4">
                                    <span class="min-w-20 font-medium">Pagamento</span>
                                    <span><?php echo wp_kses_post($order->get_payment_method_title()); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-8 mt-8">
                        <a
                            class="text-sm hover:underline hover:underline-offset-6"
                            href="<?php echo home_url(); ?>"
                            role="button">
                            <span aria-hidden="true">← </span>
                            Retornar para a Loja
                        </a>
                        <a
                            class="flex items-center justify-center p-4 rounded-sm text-sm font-semibold text-white bg-neutral-900 hover:bg-neutral-700 focus:outline-none"
                            href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                            role="button">
                            Acessar minha conta
                        </a>
                    </div>
                </div>

                <div>
                    <?php wc_get_template('order/order-details.php', ['order' => $order]); ?>
                </div>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>