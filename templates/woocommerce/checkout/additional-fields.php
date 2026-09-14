<?php

declare(strict_types=1);
defined('ABSPATH') || exit;
?>


<div x-data="{open: false}" class="my-8">

    <?php do_action('woocommerce_before_order_notes', $checkout); ?>

    <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

        <div>
            <label for="order-notes" class="flex items-center justify-start gap-3 my-4 cursor-pointer">
                <input x-model="open" id="order-notes" class="size-5 rounded-sm" type="checkbox" name="order-notes" value="" />
                <span class="text-sm">Adicione uma observação ao seu pedido</span>
            </label>
        </div>

        <div x-cloak x-transition x-show="open" class="woocommerce-additional-fields__field-wrapper">
            <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
                <?php
                if ($key === 'order_comments') :
                    $label = $field['label'] ?? '';
                    $placeholder = $field['placeholder'] ?? '';
                    $input_value = $checkout->get_value($key) ?? '';
                ?>
                    <div
                        x-init="if ($refs.<?php echo esc_attr($key); ?>.value !== '') open = true"
                        class="relative mt-3" id="<?php echo esc_attr($key) . '_field'; ?>">
                        <label class="sr-only" for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                        <textarea
                            x-ref="<?php echo esc_attr($key); ?>"
                            class="w-full p-3 text-base border border-neutral-500 rounded-sm bg-white"
                            name="<?php echo esc_attr($key); ?>"
                            id="<?php echo esc_attr($key); ?>"
                            rows="2"
                            placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($input_value); ?></textarea>
                    </div>
                <?php else : ?>
                    <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <?php do_action('woocommerce_after_order_notes', $checkout); ?>
</div>