<?php

/**
 * Field Typography
 *
 * @param $settings
 * @param string $value
 *
 * @since 1.0.0
 * @return string - html string.
 */
function tpfw_form_typography( $settings, $value = array() ) {

	$attrs = array();

	if ( !empty( $settings['name'] ) ) {
		$attrs[] = 'name="' . $settings['name'] . '"';
	}

	if ( !empty( $settings['id'] ) ) {
		$attrs[] = 'id="' . $settings['id'] . '"';
	}

	if ( !empty( $settings['customize_link'] ) ) {
		$attrs[] = $settings['customize_link'];
	}

	$data = array();
	
	/**
	 * Using the $subfields to decided which fields are appear
	 */
	$subfields = isset( $settings['default'] ) ? $settings['default'] : array();

	$data = tpfw_build_typography($value);

	$attrs[] = 'data-type="typography"';

	$output = '';
	$output .= sprintf( '<div class="tpfw-field tpfw-typography" data-id="%s" data-value="%s">', uniqid(), $value );
	$output .= sprintf( '<input type="hidden" class="tpfw_value" %s/>', implode( $attrs ) );

	$output .= '<div class="font_family">';
	$output .= sprintf( '<label>%s</label>', __( 'Font Family', 'tp-framework' ) );
	$output .= '<select></select>';
	$output .= '</div>';

	$output .= '<div class="variants">';
	$output .= sprintf( '<label>%s</label>', __( 'Variants', 'tp-framework' ) );
	$output .= sprintf( '<select placeholder="%s" multiple>%s</select>', __( 'Select Variants...','tp-framework' ), '' );
	$output .= '</div>';

	$output .= '<div class="subsets">';
	$output .= sprintf( '<label>%s</label>', __( 'Subsets', 'tp-framework' ) );
	$output .= sprintf( '<select placeholder="%s" multiple>%s</select>', __( 'Select Subsets...','tp-framework' ), '' );
	$output .= '</div>';

	$output .= '<div class="subrow">';

	if ( isset( $subfields['line-height'] ) ) {
		$output .= '<div class="line_height">';
		$output .= sprintf( '<label>%s</label>', __( 'Light Height', 'tp-framework' ) );
		$line_height = isset( $data['line-height'] ) ? $data['line-height'] : '';
		$output .= sprintf( '<input type="text" value="%s" data-key="line-height" placeholder="%s"/>', $line_height, __( '1.4em', 'tp-framework' ) );
		$output .= '</div>';
	}

	if ( isset( $subfields['font-size'] ) ) {

		$output .= '<div class="font_size">';
		$output .= sprintf( '<label>%s</label>', __( 'Font Size', 'tp-framework' ) );
		$font_size = isset( $data['font-size'] ) ? $data['font-size'] : '';
		$output .= sprintf( '<input type="text" value="%s" data-key="font-size" placeholder="%s"/>', $font_size, __( '14px', 'tp-framework' ) );
		$output .= '</div>';
	}

	if ( isset( $subfields['letter-spacing'] ) ) {
		$output .= '<div class="letter_spacing">';
		$output .= sprintf( '<label>%s</label>', __( 'Letter Spacing', 'tp-framework' ) );
		$letter_spacing = isset( $data['letter-spacing'] ) ? $data['letter-spacing'] : '';
		$output .= sprintf( '<input type="text" value="%s" data-key="letter-spacing" placeholder="%s"/>', $letter_spacing, __( '1px', 'tp-framework' ) );
		$output .= '</div>';
	}

	if ( isset( $subfields['text-transform'] ) ) {
		$output .= '<div class="text_transform">';
		$output .= sprintf( '<label>%s</label>', __( 'Text Transform', 'tp-framework' ) );
		$text_transform = isset( $data['text-transform'] ) ? $data['text-transform'] : '';

		$text_transform_opts = array(
			'none' => esc_attr__( 'None', 'tp-framework' ),
			'capitalize' => esc_attr__( 'Capitalize', 'tp-framework' ),
			'uppercase' => esc_attr__( 'Uppercase', 'tp-framework' ),
			'lowercase' => esc_attr__( 'Lowercase', 'tp-framework' ),
			'initial' => esc_attr__( 'Initial', 'tp-framework' )
		);

		$output .= '<select data-key="text-transform">';
		foreach ( $text_transform_opts as $key => $value ) {
			$output .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $text_transform, $key, false ), $value );
		}

		$output .= '</select>';
	}

	$output .= '</div>';

	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
