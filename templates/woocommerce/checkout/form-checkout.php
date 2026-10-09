<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<form class="checkout woocommerce-checkout pb-16" name="checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" method="post" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">

    <div class="relative grid grid-cols-1 lg:grid-cols-2 lg:items-start">

        <!-- Order Summary (Mobile Accordion via <details> + Desktop Sticky Sidebar) -->
        <div class="order-first max-w-xl p-4 lg:p-10 lg:order-last">

            <details class="checkout-order-summary-details" open>

                <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                <!-- Summary Bar (togglable on mobile) -->
                <summary class="relative list-none mb-4 lg:mb-6 flex items-center justify-between rounded-md border border-neutral-300 bg-neutral-50 p-4 lg:border-none lg:bg-transparent lg:p-0 lg:pointer-events-none">
                    <div class="flex items-center justify-start gap-2">
                        <svg class="mb-0.75 size-5 lg:size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <h3 class="summary-label text-sm lg:text-lg font-semibold tracking-tight" id="order_review_heading"><?php esc_html_e('Order summary', 'woocommerce'); ?></h3>
                        <span class="summary-chevron transition-transform duration-200 lg:hidden!">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </div>
                    <span id="checkout-mobile-total" class="flex-1 text-right text-base font-semibold tracking-tight whitespace-nowrap lg:hidden!"></span>
                </summary>

                <?php do_action('woocommerce_checkout_before_order_review'); ?>

                <!-- Collapsible content on mobile / Sticky card on desktop -->
                <div id="order_review" class="woocommerce-checkout-review-order order-review-panel rounded-2xl border border-neutral-300 bg-neutral-50 p-5 sm:p-7 lg:sticky lg:border-none lg:bg-transparent lg:p-0">
                    <?php do_action('woocommerce_checkout_order_review'); ?>
                </div>

                <?php do_action('woocommerce_checkout_after_order_review'); ?>

            </details>

        </div>

        <!-- Customer Details & Payment (Left Column) -->
        <div class="bg-neutral-50 lg:flex lg:justify-end">
            <div class="max-w-xl p-4 lg:p-10">

                <?php woocommerce_output_all_notices(); ?>

                <?php if ($checkout->get_checkout_fields()) :

                    do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div id="customer_details">
                        <div id="billing_details">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>

                        <div id="shipping_details">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                <?php endif; ?>

                <div class="woocommerce-additional-fields">
                    <?php wc_get_template('checkout/additional-fields.php', array('checkout' => WC()->checkout())); ?>
                </div>

                <div>
                    <?php woocommerce_checkout_payment(); ?>
                </div>
            </div>
        </div>

    </div>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>