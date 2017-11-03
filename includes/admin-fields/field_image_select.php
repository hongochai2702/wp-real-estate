<?php

/**
 * Image Select
 * 
 * @package   Tpfw/Corefields
 * @category  Functions
 * @author    ThemesPond
 * @license   GPLv3
 * @version   1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Field Image Select.
 *
 * @param $settings
 * @param string $value
 *
 * @since 1.0.0
 * @return string - html string.
 */
function tpfw_form_image_select( $settings, $value ) {

	$output = '';

	$attrs = array();

	if ( !empty( $settings['name'] ) ) {
		$attrs[] = 'name="' . $settings['name'] . '"';
	}

	if ( !empty( $settings['id'] ) ) {
		$attrs[] = 'id="' . $settings['id'] . '"';
	}

	$attrs[] = 'data-type="' . $settings['type'] . '"';


	/**
	 * Support Customizer
	 */
	if ( !empty( $settings['customize_link'] ) ) {
		$attrs[] = $settings['customize_link'];
	}

	if ( !isset( $settings['inline'] ) ) {
		$settings['inline'] = 1;
	}

	$image_size = '';

	if ( isset( $settings['image_size'][0] ) ) {

		$height = $settings['image_size'][0];

		if ( isset( $settings['image_size'][1] ) ) {
			$height = $settings['image_size'][1];
		}

		$image_size = sprintf( 'style="width:%s;height:%s"', $settings['image_size'][0], $height );
	}

	if ( is_array( $settings['options'] ) ) {

		$br = $settings['inline'] ? '' : '<br/>';

		$output .= sprintf( '<div class="tpfw-field tpfw-image_select">' );

		foreach ( $settings['options'] as $radio_key => $image_url ) {

			$checked = $radio_key === $value ? 'checked' : '';
			
			$output .= sprintf( '<label><input class="tpfw_value" type="radio" value="%1$s" %3$s %4$s/><span><img alt="%1$s" src="%2$s" %5$s/></span></label>', $radio_key, esc_url( $image_url ), $checked, implode( ' ', $attrs ), $image_size );

			$output .= $br;
		}

		$output .= '</div>';
	}

	return $output;
}
