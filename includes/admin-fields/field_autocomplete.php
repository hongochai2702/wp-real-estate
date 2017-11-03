<?php

/**
 * Autocomplete
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
 * Field Autocomplete
 *
 * @param $settings
 * @param string $value
 *
 * @since 1.0.0
 * @return string - html string.
 */
function tpfw_form_autocomplete( $settings, $value ) {

	$output = '';

	$attrs = array();

	if ( !empty( $settings['name'] ) ) {
		$attrs[] = 'name="' . $settings['name'] . '"';
	}

	if ( !empty( $settings['id'] ) ) {
		$attrs[] = 'id="' . $settings['id'] . '"';
	}

	$attrs[] = 'data-type="' . $settings['type'] . '"';

	$css_class = '';

	if ( !empty( $settings['el_class'] ) ) {
		$css_class = $settings['el_class'];
	}

	/**
	 * Support Customizer
	 */
	if ( !empty( $settings['customize_link'] ) ) {
		$attrs[] = $settings['customize_link'];
	}

	$ajax_type = 'post_type';
	$ajax_value = array( 'post' );
	$min_length = isset( $settings['min_length'] ) ? absint( $settings['min_length'] ) : 3;
	$placeholder = sprintf( __( 'Please enter %d or more characters', 'tp-framework' ), $min_length );
	$multiple = !empty( $settings['multiple'] ) ? 'multiple' : '';

	if ( !empty( $settings['options'] ) ) {

		$settings['data'] = false;
		$ajax_type = false;
		$ajax_value = array( 'options' );
		
	} else {

		if ( !empty( $settings['data'] ) && is_array( $settings['data'] ) ) {
			$ajax_type = key( $settings['data'] );
			if ( !empty( $settings['data'][$ajax_type] ) && is_array( $settings['data'][$ajax_type] ) ) {
				$ajax_value = $settings['data'][$ajax_type];
			}
		}

		if ( isset( $settings['placeholder'] ) ) {
			$placeholder = $settings['placeholder'];
		}
	}

	$dataAttrs = array(
		'data-ajax_type="' . $ajax_type . '"',
		'data-ajax_value="' . implode( ',', $ajax_value ) . '"',
		'data-min_length="' . $min_length . '"'
	);

	$output .= sprintf( '<div class="tpfw-field tpfw-autocomplete %s" %s>', $css_class, implode( ' ', $dataAttrs ) );

	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}

	$output .= sprintf( '<input type="hidden" class="tpfw_value" value="%s" %s/>', esc_attr( $value ), implode( ' ', $attrs ) );

	$output .= sprintf( '<select %s placeholder="%s">', $multiple, $placeholder );

	if ( !$settings['data'] ) {

		$options = isset( $settings['options'] ) ? $settings['options'] : array();

		if ( !$multiple ) {
			foreach ( $options as $key => $val ) {
				$output .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $val );
			}
		} else {
			$value = explode( ',', $value );
			foreach ( $options as $key => $val ) {
				$selected = in_array( $key, $value ) ? 'selected' : '';
				$output .= sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $val );
			}
		}
	} else {

		if ( !empty( $value ) ) {

			$value = explode( ',', $value );

			foreach ( $value as $id ) {
				if ( $ajax_type == 'post_type' ) {
					$post = get_post( $id );
					$output .= sprintf( '<option value="%s" %s>%s</option>', $post->ID, selected( $post->ID, $id, false ), get_the_title( $post ) );
				} else if ( $ajax_type == 'taxonomy' ) {
					$term = get_term( $id );
					if ( $term ) {
						$output .= sprintf( '<option value="%s" %s>%s</option>', $term->term_id, selected( $term->term_id, $id, false ), $term->name );
					}
				}
			}
		}
	}


	$output .= '</select></div>';

	return $output;
}

/**
 * Autocomplete ajax post type
 *
 * @since 1.0.0
 * @return void
 */
function tpfw_form_autocomplete_ajax_post_type() {

	$s = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
	$post_types = !empty( $_GET['types'] ) ? explode( ',', $_GET['types'] ) : array( 'post' );

	$posts = get_posts( array(
		'posts_per_page' => 20,
		'post_type' => $post_types,
		'post_status' => 'publish',
		's' => $s
			) );

	$result = array();

	foreach ( $posts as $post ) {
		$result[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}

	wp_send_json( $result );
}

/**
 * Autocomplete ajax taxonomy
 *
 * @since 1.0.0
 * @return void
 */
function tpfw_form_autocomplete_ajax_taxonomy() {

	$s = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

	$types = !empty( $_GET['types'] ) ? explode( ',', $_GET['types'] ) : array( 'category' );

	$args['taxonomy'] = $types;
	$args['hide_empty'] = false;
	$args['name__like'] = $s;


	$terms = get_terms( $args );

	$result = array();

	foreach ( $terms as $term ) {
		$result[] = array(
			'value' => $term->term_id,
			'label' => $term->name,
		);
	}

	wp_send_json( $result );
}

add_action( 'wp_ajax_tpfw_autocomplete_post_type', 'tpfw_form_autocomplete_ajax_post_type' );
add_action( 'wp_ajax_tpfw_autocomplete_taxonomy', 'tpfw_form_autocomplete_ajax_taxonomy' );
