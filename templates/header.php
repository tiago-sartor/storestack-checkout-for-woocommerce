<?php
/**
 * Checkout Header Template
 * 
 * This template can be overridden by copying it to yourtheme/storestack-checkout/header.php.
 *
 * HOWEVER, on occasion we'll need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package StoreStackCheckoutForWooCommerce
 * @version 1.0.0
 */

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>

    <header class="storestack-checkout-header relative bg-neutral-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-4 flex flex-col items-center justify-center gap-2 sm:flex-row sm:justify-between text-white">
            <a class="size-fit max-h-9 overflow-clip" href="<?php echo home_url(); ?>" aria-label="<?php echo esc_html__('Return to homepage', 'storestack-checkout-for-woocommerce'); ?>">
                <?php if (has_custom_logo()) : ?>
                    <img src="<?php echo esc_url(wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full')); ?>" class="size-full max-h-9 brightness-0 invert" />
                <?php else : ?>
                    <h1><?php echo get_bloginfo('name') ?></h1>
                <?php endif; ?>
            </a>
            <span class="flex items-center justify-center gap-1 text-sm font-medium">
                <svg class="size-8" viewBox="0 -960 960 960" fill="currentColor">
                    <path d="M480-121.54q-120.54-35.77-200.27-146.04Q200-377.85 200-516v-216.31l280-104.61 280 104.61V-516q0 138.15-79.73 248.42Q600.54-157.31 480-121.54Zm0-42.46q104-33 172-132t68-220v-189l-240-89.23L240-705v189q0 121 68 220t172 132Zm0-315.23Zm-78.46 145.38h156.92q13.73 0 23.02-9.28 9.29-9.29 9.29-23.02v-116.93q0-13.73-9.29-23.02-9.29-9.28-23.02-9.28h-6.92v-40q0-29.93-20.42-50.35-20.43-20.42-50.35-20.42t-50.35 20.42Q410-585.31 410-555.38v40h-8.46q-13.73 0-23.02 9.28-9.29 9.29-9.29 23.02v116.93q0 13.73 9.29 23.02 9.29 9.28 23.02 9.28Zm39.23-181.53v-40q0-17 11.5-28.5t28.5-11.5q17 0 28.5 11.5t11.5 28.5v40h-80Z" />
                </svg>
                <?php echo esc_html__('Secure Checkout', 'storestack-checkout-for-woocommerce'); ?>
            </span>
        </div>
    </header>