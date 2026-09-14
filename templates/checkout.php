<?php

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;


$default_header_template = SSCFW_PLUGIN_PATH . 'templates/header.php';

/**
 * Filters the path used to render the checkout header template.
 *
 * @since 1.0.0
 *
 * @param string $template_path Path to the template file.
 */
$filtered_header_template = apply_filters( 'storestack_checkout_header_template', $default_header_template );

include_once file_exists($filtered_header_template) ? $filtered_header_template : $default_header_template;
?>


<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <?php
    while (have_posts()) {
        the_post();
        the_content();
    }
    ?>
</main>


<?php
$default_footer_template = SSCFW_PLUGIN_PATH . 'templates/footer.php';

/**
 * Filters the path used to render the checkout footer template.
 *
 * @since 1.0.0
 *
 * @param string $template_path Path to the template file.
 */
$filtered_footer_template = apply_filters( 'storestack_checkout_footer_template', $default_footer_template );

include_once file_exists($filtered_footer_template) ? $filtered_footer_template : $default_footer_template;
