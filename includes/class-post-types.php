<?php

if (!defined('WP_REAL_ESTATE_BASE')) {
    exit; // Exit if accessed directly
}

/**
 * LANGUAGE LOCALIZATION
 * Provide localization for Javascript variable
 *
 * @class    WP_Real_Estate_Post_Types
 * @package  WP_Real_Estate/Classes
 * @category Class
 * @version  1.0
 */
if (!class_exists('WP_Real_Estate_Post_Types')) {

    class WP_Real_Estate_Post_Types {

        public function __construct( ) {

            // include hook.
            add_action( 'init', array( $this,'real_estate_post_type' ) );
        }

        // Create key.
        private function create_key($val) {
            return $this->_prefix_key . $val;
        }

        // Create id
        private function create_id($val) {
            return $this->_prefix_id . $val;
        }


        public function real_estate_post_type() {
            $labels = array(
                'name'              => 'Tin bất động sản',
                'singular_name'     => 'Tin bất động sản',
                'add_new'           => 'Thêm tin mới',
                'all_items'         => 'Tất cả tin',
                'add_new_item'      => 'Tạo tin',
                'edit_item'         => 'Chỉnh sửa',
                'new_item'          => 'Tin mới',
                'view_item'         => 'Xem tin',
                'search_items'      => 'Tìm tin bất động sản',
                'not_found'         => 'Không có tin nào',
                'not_found_in_trash'=> 'Không có tin nào trong thùng rác',
                'parent_item_colon' => 'Tin cha'
                //'menu_name' => default to 'name'
            );
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'has_archive'           => true,
                'publicly_queryable'    => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'nha-dat'),
                'capability_type'       => 'post',
                'hierarchical'          => false,
                'supports'              => array(
                                            'title',
                                            'editor',
                                            'excerpt',
                                            'thumbnail',
                                            'author',
                                            'custom-fields',
                                            'comments',
                                            'revisions' ),
                'menu_position' => 5,
                'exclude_from_search' => false
            );
            register_post_type( 'nha_dat', $args );

            register_taxonomy( 'bat_dong_san', // register custom taxonomy - category
                'nha_dat',
                array(
                    'hierarchical' => true,
                    'rewrite' => array( 'slug' => 'bat-dong-san'),
                    'labels' => array(
                        'name' => 'Loại bất động sản',
                        'singular_name' => 'Loại bất động sản',
                    )
                )
            );

            register_taxonomy( 'project_state', // register custom taxonomy - state
                'nha_dat',
                array(
                    'hierarchical' => true,
                    'labels' => array(
                        'name' => 'Khu vực',
                        'singular_name' => 'Khu vực',
                    )
                )
            );

            register_taxonomy( 'project_category', // register custom taxonomy - category
                'nha_dat',
                array(
                    'hierarchical' => true,
                    'labels' => array(
                        'name' => 'Dự án bất động sản',
                        'singular_name' => 'Dự án bất động sản',
                    )
                )
            );

            register_taxonomy( 'nha_dat_tag', // register custom taxonomy - tag
                'nha_dat',
                array(
                    'hierarchical' => false,
                    'labels' => array(
                        'name' => 'Từ khóa bất động sản',
                        'singular_name' => 'Từ khóa bất động sản',
                    )
                )
            );
        }


    } // Class WP_Real_Estate_Post_Types : END
    new WP_Real_Estate_Post_Types();
} // IF WP_Real_Estate_Post_Types : END
