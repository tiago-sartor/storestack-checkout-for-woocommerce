<?php
/**
 * Template for additional checkout fields
 *
 * @package StoreStackCheckoutForWooCommerce
 * @version 1.0.0
 */

declare(strict_types=1);

defined('ABSPATH') || exit;


do_action('woocommerce_before_order_notes', $checkout);
?>

<?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

        <label for="order-notes" class="peer inline-flex cursor-pointer items-center gap-3 text-sm transition-colors select-none hover:underline">
            <input
                id="order-notes"
                name="order-notes"
                class="size-4.5 cursor-pointer rounded-md border-neutral-300 accent-neutral-900"
                type="checkbox"
                value="" />
            <span class="font-medium"><?php echo esc_html__('Add order notes or instructions', 'woocommerce'); ?></span>
        </label>

        <!-- Hidden by default; revealed when the checkbox above is checked via CSS peer pattern -->
        <div class="woocommerce-additional-fields__field-wrapper mt-3 hidden peer-has-[input:checked]:block">
            <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
                <?php
                if ($key === 'order_comments') :
                    $label = $field['label'] ?? __('Order notes', 'woocommerce');
                    $placeholder = $field['placeholder'] ?? __('Notes about your order, e.g. special notes for delivery.', 'woocommerce');
                    $input_value = $checkout->get_value($key) ?? '';
                ?>
                    <div
                        class="relative"
                        id="<?php echo esc_attr($key) . '_field'; ?>">
                        <label class="sr-only" for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                        <textarea
                            class="w-full p-3.5 text-sm bg-white border border-neutral-300 rounded-lg placeholder-neutral-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 transition-all shadow-2xs resize-y"
                            name="<?php echo esc_attr($key); ?>"
                            id="<?php echo esc_attr($key); ?>"
                            rows="3"
                            placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($input_value); ?></textarea>
                    </div>
                <?php else : ?>
                    <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

<?php endif; ?>

<?php
do_action('woocommerce_after_order_notes', $checkout);
