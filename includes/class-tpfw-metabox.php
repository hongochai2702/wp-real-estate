<?php

/**
 * Metabox Framework
 *
 * @class     Tpfw_Metaboxes
 * @package   Tpfw/Classes
 * @category  Class
 * @author    ThemesPond
 * @license   GPLv3
 * @version   1.0.0
 */
if ( !class_exists( 'Tpfw_Metabox' ) ) {

	/*
	 * Tpfw_Metaboxes Class
	 */

	class Tpfw_Metabox {

		/**
		 * @access private
		 * @var array meta boxes settings
		 */
		private $settings = array();

		/**
		 * @access private
		 * @var string Container of the field in markup HTML
		 */
		private $field_wrapper;

		/**
		 * @access private
		 * @var string Html of all output field
		 */
		private $output;

		/**
		 * @access private
		 * @var array Group fields
		 */
		private $group_fields = array();

		/**
		 * Init
		 */
		public function __construct( $args = array() ) {

			if ( !empty( $args ) ) {

				$defaults = array(
					'id' => 'tpfw_metabox',
					'screens' => array( 'page' ),
					'title' => __( 'Tpfw Metabox', 'tp-framework' ),
					'context' => 'advanced',
					'priority' => 'low',
					'fields' => array(
					)
				);

				$this->settings = wp_parse_args( $args, $defaults );

				add_action( 'add_meta_boxes', array( $this, 'register' ) );

				foreach ( $this->settings['screens'] as $screen => $value ) {

					if ( $value == 'front_page' || $value == 'posts_page' ) {
						$screen = 'page';
					} else if ( !is_string( $screen ) ) {
						$screen = $value;
					} else {
						$screen = $value;
					}

					add_action( 'save_post_' . $screen, array( $this, 'save' ), 1, 2 );
				}
			}
		}

		/**
		 * Register hook
		 * @return void
		 */
		public function register() {

			$this->field_wrapper = '<div class="tpfw_form_row" %3$s><div class="col-label">%1$s</div><div class="col-field">%2$s</div></div>';

			$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

			$this->output = $this->pre_output( $post_id );

			foreach ( $this->settings['screens'] as $screen => $value ) {

				if ( ($value == 'front_page' && $post_id == get_option( 'page_on_front' )) || ($value == 'posts_page' && $post_id == get_option( 'page_for_posts' ) ) ) {
					$screen = 'page';
					add_meta_box( $this->settings['id'], $this->settings['heading'], array( $this, 'output' ), $screen, $this->settings['context'], $this->settings['priority'], $this->settings['fields'] );
				} else if ( !empty( $screen ) && is_string( $screen ) && $value == $post_id ) {
					add_meta_box( $this->settings['id'], $this->settings['heading'], array( $this, 'output' ), $screen, $this->settings['context'], $this->settings['priority'], $this->settings['fields'] );
				} else {
					$screen = $value;
					add_meta_box( $this->settings['id'], $this->settings['heading'], array( $this, 'output' ), $screen, $this->settings['context'], $this->settings['priority'], $this->settings['fields'] );
				}
			}
		}

		/**
		 * Output callback
		 * @return void
		 */
		public function output( $post, $args ) {
			echo $this->output;
		}

		/**
		 * Process field output
		 * 
		 * @global array $tpfw_registered_fields
		 * @param int $post_id
		 * @return string Html
		 */
		public function pre_output( $post_id ) {

			global $tpfw_registered_fields;

			if ( empty( $tpfw_registered_fields ) ) {
				$tpfw_registered_fields = array();
			}

			$output = '';

			$metabox = $this->settings;

			$output .= sprintf( '<div class="tpfw-metabox tpfw-metabox_%s">', $metabox['id'] );

			$output .= sprintf( '<input type="hidden" name="%s_nonce" value="%s" />', $metabox['id'], wp_create_nonce( $metabox['id'] ) );

			if ( !empty( $metabox['manage_box'] ) ) {
				$value = get_post_meta( $post_id, $metabox['id'], true );
				$output .= sprintf( '<input class="tpfw-manage_box" type="hidden" name="%s" value="%s" data-label="%s"/>', $metabox['id'], $value, esc_attr__( 'Enable', 'tp-framework' ) );
			}

			foreach ( $metabox['fields'] as $field ) {
				/**
				 * Field value
				 */
				$value = get_post_meta( $post_id, $field['name'], false );

				if ( isset( $value[0] ) ) {
					$value = $value[0];
				} elseif ( empty( $value[0] ) ) {
					$value = isset( $field['value'] ) ? $field['value'] : '';
				} else {
					$value = '';
				}

				$field['value'] = $value;

				/**
				 * Add field type to global array
				 */
				$tpfw_registered_fields[] = $field['type'];

				/**
				 * Before render field type
				 */
				do_action( 'tpfw_before_render_field_' . $field['type'], $field );

				/**
				 * Render field
				 */
				$field_output = $this->field_render( $field );

				/**
				 * Add field to group
				 */
				if ( $field_output != '' ) {
					$group = !empty( $field['group'] ) ? $field['group'] : '';
					if ( empty( $this->group_fields[$group] ) ) {
						$this->group_fields[$group] = array();
					}
					$this->group_fields[$group][] = $field_output;
				}
			}

			if ( count( $this->group_fields ) == 1 && !key( $this->group_fields ) ) {
				$output .= implode( '', $this->group_fields[''] );
			} else {
				$nav = '';
				$content = '';
				$index = 0;
				foreach ( $this->group_fields as $name => $fields ) {

					$name = empty( $name ) ? __( 'General', 'tp-framework' ) : $name;
					$index++;
					$active = $index == 1 ? 'active' : '';
					$id = $this->settings['id'] . '-group_' . $index;
					$nav .= sprintf( '<li><a href="#%s" class="%s">%s</a></li>', $id, $active, $name );
					$content .= sprintf( '<div id="%s" class="group_item %s">%s</div>', $id, $active, implode( '', $fields ) );
				}

				$output .= '<div class="tpfw_group">';
				$output .= '<ul class="group_nav">' . $nav . '</ul>';
				$output .= '<div class="group_panel">' . $content . '</div>';
				$output .= '</div>';
			}

			$output .= '</div>';

			$tpfw_registered_fields = array_unique( $tpfw_registered_fields );

			return $output;
		}

		/**
		 * Process field
		 * @access private
		 * @return string Field Html
		 */
		private function field_render( $field ) {

			$field_output = '';

			$field['id'] = $field['name'];

			$required = isset( $field['required'] ) && absint( $field['required'] ) ? '<span>*</span>' : '';

			$lable = !empty( $field['heading'] ) ? sprintf( '<label for="%1$s">%2$s %3$s</label>', $field['name'], $field['heading'], $required ) : '';

			$desc = !empty( $field['desc'] ) ? sprintf( '<p class="description">%s</p>', $field['desc'] ) : '';

			$attrs = sprintf( 'data-param_name="%s" ', $field['name'] );

			$attrs .= !empty( $field['dependency'] ) && is_array( $field['dependency'] ) ? 'data-dependency="' . esc_attr( json_encode( $field['dependency'] ) ) . '"' : '';

			if ( function_exists( "tpfw_form_{$field['type']}" ) ) {

				if ( isset( $field['sanitize_callback'] ) && function_exists( $field['sanitize_callback'] ) ) {
					$field['value'] = call_user_func( $field['sanitize_callback'], $field['value'] );
				}

				$callback = call_user_func( "tpfw_form_{$field['type']}", $field, $field['value'] ) . $desc;
				$field_output = sprintf( $this->field_wrapper, $lable, $callback, $attrs );
			} else if ( has_filter( "tpfw_form_{$field['type']}" ) ) {

				$field_output = apply_filters( "tpfw_form_{$field['type']}", '', $field ) . $desc;
				$field_output = sprintf( $this->field_wrapper, $lable, $field_output, $attrs );
			}

			return $field_output;
		}

		/**
		 * Save post meta
		 * @param int $post_id
		 * @param object|WP_Post $post
		 */
		public function save( $post_id, $post ) {

			$metabox = $this->settings;

			/* don't save if $_POST is empty */
			if ( empty( $_POST ) )
				return $post_id;

			/* don't save during autosave */
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;

			/* verify nonce */
			if ( !isset( $_POST[$metabox['id'] . '_nonce'] ) || !wp_verify_nonce( $_POST[$metabox['id'] . '_nonce'], $metabox['id'] ) )
				return $post_id;

			/* check permissions */
			if ( isset( $_POST['post_type'] ) && 'page' == sanitize_text_field( $_POST['post_type'] ) ) {
				if ( !current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}

			$allow_save = true;

			if ( !empty( $metabox['manage_box'] ) ) {

				$value = sanitize_text_field( $_POST[$metabox['id']] );

				if ( !$value ) {
					$allow_save = false;
				}

				update_post_meta( $post_id, $metabox['id'], $value );
			}

			if ( $allow_save ) {

				foreach ( $metabox['fields'] as $field ) {

					if ( !isset( $field['name'] ) ) {
						continue;
					}

					$value = '';

					if ( isset( $_POST[$field['name']] ) || $field['type'] == 'checkbox' ) {

						$input_value = isset( $_POST[$field['name']] ) ? $_POST[$field['name']] : '';

						if ( isset( $field['sanitize_callback'] ) && function_exists( $field['sanitize_callback'] ) ) {
							$value = call_user_func( $field['sanitize_callback'], $input_value );
						} else {
							if ( $field['type'] == 'upload' ) {
								$value = tpfw_sanitize_upload( $input_value );
							} elseif ( isset( $field['multiple'] ) && $field['multiple'] ) {
								$value = maybe_unserialize( $input_value );
							} elseif ( $field['type'] == 'checkbox' ) {
								$value = !empty( $input_value ) ? 1 : 0;
							} elseif ( $field['type'] == 'link' ) {

								$value = strip_tags( $input_value );
							} elseif ( $field['type'] == 'textarea' ) {

								$value = wp_kses( trim( wp_unslash( $input_value ) ), wp_kses_allowed_html( 'post' ) );
							} elseif ( $field['type'] == 'repeater' && !empty( $input_value ) ) {
								$value = json_encode( $input_value, JSON_UNESCAPED_UNICODE );
							} else {
								$value = sanitize_text_field( $input_value );
							}
						}

						/**
						 * Allow third party filter value
						 */
						$value = apply_filters( "tpfw_sanitize_field_{$field['type']}", $value, $input_value );

						update_post_meta( $post_id, $field['name'], $value );
					} else {
						delete_post_meta( $post_id, $field['name'] );
					}
				}
			}
			do_action( sprintf( 'tpfw_%s_updated', $metabox['id'] ), $post_id, $post, $allow_save );
		}

	}

}