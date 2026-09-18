<?php

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;

?>

<footer class="bg-neutral-50">

    <div class="mx-auto max-w-1440px px-4 py-8 sm:px-6 lg:px-8">

        <ul class="flex flex-col items-center justify-center gap-2 text-xs text-neutral-600 md:flex-row md:gap-4">
            <li class="inline-flex">
                <a class="underline hover:text-gold-500" href="<?php echo esc_url(get_permalink(wc_terms_and_conditions_page_id())); ?>">
                    <?php echo esc_html__('Terms and Conditions', 'storestack-checkout-for-woocommerce'); ?>
                </a>
            </li>
            <li class="inline-flex">
                <a class="underline hover:text-gold-500" href="<?php echo esc_url(get_privacy_policy_url()); ?>">
                    <?php echo esc_html__('Privacy Policy', 'storestack-checkout-for-woocommerce'); ?>
                </a>
            </li>
        </ul>

        <div class="flex flex-col items-center justify-center pt-8 text-xs sm:text-sm font-light text-neutral-500 text-center">
            <?php
            if ($footer_text = get_option('sscfw_footer_text')) {
                echo wp_kses_post($footer_text);
            } else {
                echo '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. ' . esc_html__('All rights reserved', 'storestack-checkout-for-woocommerce') . '.';
            }
            ?>
        </div>

    </div>

</footer>

<?php wp_footer(); ?>

</body>

</html>