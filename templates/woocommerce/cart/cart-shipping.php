<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 */

declare(strict_types=1);
defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );

$shipping_total = WC()->cart->get_shipping_total();
?>

<div class="woocommerce-shipping-totals shipping flex flex-col">

	<div class="flex items-center justify-between">
		<?php echo wp_kses_post( $package_name ); ?>
		<?php if ( count( $available_methods ) > 1 ) : /* Output only if we have 2 or more methods available */ ?>
			<span class="font-medium whitespace-nowrap">
				<?php echo wp_kses_post( $shipping_total > 0 ? wc_price( $shipping_total ) : __( 'Free', 'woocommerce' ) ); ?>
			</span>
		<?php endif; ?>
	</div>

	<div data-title="<?php echo esc_attr( $package_name ); ?>">

		<?php if ( ! empty( $available_methods ) && is_array( $available_methods ) ) : ?>
			<ul id="shipping_method" class="woocommerce-shipping-methods mt-3 divide-y divide-neutral-300 border border-neutral-300 rounded-md overflow-clip bg-white">
				<?php foreach ( $available_methods as $method ) : ?>

					<?php $free_shipping = $method->cost <= 0 && in_array( $method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true ); ?>

					<li class="group has-checked:bg-neutral-100">
						<label class="flex cursor-pointer items-center justify-start gap-2 p-2" for="shipping_method_<?php echo esc_attr( $index ); ?>_<?php echo esc_attr( sanitize_title( $method->id ) ); ?>">
							<input class="shipping_method size-3.5 cursor-pointer accent-current" type="radio" name="shipping_method[<?php echo esc_attr( $index ); ?>]" data-index="<?php echo esc_attr( $index ); ?>" id="shipping_method_<?php echo esc_attr( $index ); ?>_<?php echo esc_attr( sanitize_title( $method->id ) ); ?>" value="<?php echo esc_attr( $method->id ); ?>" <?php checked( $method->id, $chosen_method ); ?>>
							<div class="flex flex-1 items-center justify-between text-xs">
								<span class="group-has-checked:font-medium"><?php echo esc_html( $method->get_label() ); ?></span>
								<span class="font-semibold whitespace-nowrap"><?php echo wp_kses_post( $free_shipping ? __( 'Free', 'woocommerce' ) : wc_price( $method->cost ) ); ?></span>
							</div>
						</label>

						<?php do_action( 'woocommerce_after_shipping_rate', $method, $index ); ?>

					</li>
				<?php endforeach; ?>
			</ul>
			<?php
		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			echo '<p class="mt-2 text-xs font-medium">';
			echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'woocommerce' ) ) );
			echo '</p>';
		elseif ( ! is_cart() ) :
			echo '<p class="mt-2 text-xs font-medium">';
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
			echo '</p>';
		else :
			echo '<p class="mt-2 text-xs">';
			echo wp_kses_post(
				/**
				 * Provides a means of overriding the default 'no shipping available' HTML string.
				 *
				 * @since 3.0.0
				 *
				 * @param string $html                  HTML message.
				 * @param string $formatted_destination The formatted shipping destination.
				 */
				apply_filters(
					'woocommerce_cart_no_shipping_available_html',
					// Translators: $s shipping destination.
					sprintf( esc_html__( 'No shipping options were found for %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ),
					$formatted_destination
				)
			);
			echo '</p>';
		endif;
		?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ( $show_shipping_calculator ) : ?>
			<?php woocommerce_shipping_calculator( $calculator_text ); ?>
		<?php endif; ?>
	</div>
</div>