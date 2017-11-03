<?php

if ( !defined('WP_REAL_ESTATE_BASE') ) {
	exit; // Exit if accessed directly
}

/**
 * LANGUAGE LOCALIZATION
 * Provide localization for Javascript variable
 *
 * @class    WP_Real_Estate_MetaBox
 * @package  WP_Real_Estate/Classes
 * @category Class
 * @version  1.0
 */

if ( !class_exists('WP_Real_Estate_MetaBox') ) {
    
    class WP_Real_Estate_MetaBox {
        
        private $_meta_box_id   = 'wp-real-estate-metabox';
        private $_prefix_key    = '_wp_real_estate_metabox_';
        private $_prefix_id     = 'wp-real-estate-metabox-';
        
        public function __construct() {
            // Do something.
            //echo "<br />" . __METHOD__;
            add_action( 'add_meta_boxes', array($this, 'create_metabox') );
            
            add_action( 'save_post', array($this, 'save_form') );
        }
        
        public function create_metabox() {
            add_meta_box( $this->_meta_box_id, 'Thông tin bất động sản', array( $this, 'display_form' ), 'nha_dat', 'normal' );
            
            add_action('admin_enqueue_scripts', array($this, 'add_css_file'));
        }
        
        // Display form config metabox.
        public function display_form($post) {
            
            wp_nonce_field( $this->_meta_box_id, $this->_meta_box_id . '-nonce' );
            
            echo '<div class="realestate-mb-wrap">';
            echo '<table class="form-table">';
            echo '<p><b><i>' . translate('Xin vui lòng nhập đầy đủ thông tin vào các ô sau') . ':</i></b></p>';
            
            $htmlObj = new ChiliHtml();
            
            //Tao phan tu chua Level
            $inputID 	= $this->create_id('profile');
            $inputName 	= $this->create_id('profile');
            $inputValue = get_post_meta($post->ID,$this->create_key('profile'),true);
            $arr 		= array('id' => $inputID,'rows'=>6, 'cols'=>60);
            $html		= $htmlObj->label(translate('Author profile'))
            . $htmlObj->textarea($inputName,$inputValue,$arr);
            echo $htmlObj->pTag($html);
            
            echo '</table>';
            echo '</div>';
        }
        
        public function add_css_file () {
            wp_register_style( $this->_prefix_id . 'metabox-style' , WP_REAL_ESTATE_URL . '/assets/css/admin-metabox-style.css', array(),'1.0');
            wp_enqueue_style($this->_prefix_id . 'metabox-style');
        }
        
        public function save_form($post_id) {
            $postVal = $_POST;
            // Do something.
        }
        
        // Method create key.
        private function create_key($val) {
            return $this->_prefix_key . $val;
        }
        
        // Method create id.
        private function create_id($val) {
            return $this->_prefix_id . $val;
        }
        
        
        
    }
    // Load Class.
    new WP_Real_Estate_MetaBox();
} // End if WP_Real_Estate_MetaBox.