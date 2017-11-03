<?php

/**
 * Sample Post, Page Metabox
 *
 * @package     Tpfw
 * @category    Sample
 * @author      ThemesPond
 * @license     GPLv2 or later
 */

/**
 * Global fields
 * @return array
 */
function tpfw_example_fields() {
	return array(
		array(
			'name' => 'tpfw_checkbox',
			'type' => 'checkbox',
			'heading' => __( 'Checkbox:', 'tp-framework' ),
			'value' => 0,
			'desc' => __( 'A short description for single Checkbox', 'tp-framework' )
		),
		array(
			'name' => 'tpfw_select',
			'type' => 'select',
			'heading' => __( 'Select:', 'tp-framework' ),
			'value' => 'eric',
			'desc' => __( 'A short description for Select box', 'tp-framework' ),
			'options' => array(
				'' => __( 'Select...', 'tp-framework' ),
				'donna' => __( 'Show Text field', 'tp-framework' ),
				'eric' => __( 'Show Textarea', 'tp-framework' ),
				'charles' => __( 'Charles Wheeler', 'tp-framework' ),
				'anthony' => __( 'Anthony Perkins', 'tp-framework' )
			)
		),
		array(
			'name' => 'tpfw_textfield',
			'type' => 'textfield',
			'heading' => __( 'Text field:', 'tp-framework' ),
			'value' => 'A default text',
			'desc' => __( 'A short description for Text Field', 'tp-framework' ),
			'show_label' => true, //Work on repeater field
			'dependency' => array(
				'tpfw_select' => array( 'values' => 'donna' )
			)
		),
		array(
			'name' => 'tpfw_textarea',
			'type' => 'textarea',
			'heading' => __( 'Text Area:', 'tp-framework' ),
			'value' => 'A default text',
			'desc' => __( 'A short description for Text Area', 'tp-framework' ),
			'dependency' => array(
				'tpfw_select' => array( 'values' => 'eric' )
			)
		),
		array(
			'name' => 'tpfw_checkbox_radio',
			'type' => 'radio',
			'heading' => __( 'Radio multiple:', 'tp-framework' ),
			'inline' => 1,
			'value' => 'eric',
			'options' => array(
				'donna' => __( 'Donna Delgado', 'tp-framework' ),
				'eric' => __( 'Eric Austin', 'tp-framework' ),
				'charles' => __( 'Charles Wheeler', 'tp-framework' ),
				'anthony' => __( 'Anthony Perkins', 'tp-framework' )
			),
			'description' => __( 'Checkbox multiple description', 'tp-framework' ),
			'show_label' => true//Work on repeater field
		)
	);
}

/**
 * Post Metabox
 */
function tpfw_example_metabox() {

	$fields = tpfw_example_fields();

	
	$box1 = new Tpfw_Metabox( array(
		'id' => 'tpfw_metabox',
		'screens' => array( 'nha_dat' ), //Display in post, page, front_page, posts_page
		'heading' => __( 'Metabox', 'tp-framework' ),
		'context' => 'advanced', //side
		'priority' => 'low',
		'manage_box' => false,
		'fields' => $fields
	) );
}

add_action( 'tpfw_metabox_init', 'tpfw_example_metabox' );

