<?php

/*
 * Plugin Name: WP Real Estate
 * Description: A WordPress plugin that allows you to reduce image file sizes and optimize all images in the media library.
 * Version: 1.0.0
 * Author: HaiHN
 * Author URI: https://themespond.com/
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Requires at least: 4.0
 * Tested up to: 4.8.2
 * Text Domain: wp-real-estate
 * Domain Path: /languages/
 *
 * @package WP_Real_Estate
 */

final class WP_Real_Estate {
    /**
     * WP Real Estate version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var WP Real Estate
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * Main WP Real Estate Instance.
     *
     * Ensures only one instance of WP Real Estate is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see wp_real_estate()
     * @return WP Real Estate - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Init plugin
     * @since 1.0
     */

    private $title;

    public function __construct() {
        $this->title = esc_html__('WP Real Estate', 'wp-real-estate');
        $this->defined();
        $this->includes();
        $this->hook();
        do_action( 'tpfw_loaded' );
    }

    private function defined() {

        /* Define _ TPFW : START */
        define( 'TPFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        define( 'TPFW_VERSION', $this->version );
        define( 'TPFW_DIR', plugin_dir_path( __FILE__ ) );
        define( 'TPFW_URL', plugin_dir_url( __FILE__ ) );
        define( 'TPFW_ADDONS_DIR', TPFW_DIR . 'addons' );
        define( 'TPFW_ADDONS_URL', TPFW_URL . 'addons' );
        /* Define _ TPFW : END */

        define('WP_REAL_ESTATE_DIR', plugin_dir_path(__FILE__));
        define('WP_REAL_ESTATE_INC_DIR', WP_REAL_ESTATE_DIR . '/includes');
        define('WP_REAL_ESTATE_LIB_DIR', WP_REAL_ESTATE_INC_DIR . '/lib');
        
        define('WP_REAL_ESTATE_URL', plugin_dir_url(__FILE__));
        define('WP_REAL_ESTATE_BASE', 'wp-real-estate');
        define('WP_REAL_ESTATE_VER', '1.0.0');
    }

    /**
     * Register plugin page
     *
     * @since 1.0.0
     */
    public function register_page() {
        add_menu_page($this->title, esc_html__('Real Estate Setting', 'wp-real-estate'), 'manage_options', WP_REAL_ESTATE_BASE, array($this, 'plugin_load'), 'dashicons-images-alt2', 12);
    }

    /**
     * Create environment to init post metabox
     * @since 1.0.0
     */
    public function metabox_init() {
        do_action( 'tpfw_metabox_init' );
    }

    /**
     * Load content
     *
     * @return void
     * @since 1.0.0
     */
    public function plugin_load() {
        
    }

    /**
     * Include class
     *
     * @since 1.0.0
     */
    private function includes() {

        include WP_REAL_ESTATE_DIR . '/includes/helpers-function.php';
        
        // include TPFW_DIR . 'includes/tpfw-sanitize-functions.php';
        // include TPFW_DIR . 'includes/tpfw-helpers-functions.php';
        
        include TPFW_DIR . 'includes/class-tpfw-metabox.php';

        do_action( 'tpfw_includes' );

        wp_real_estate_class('post-types');
        //wp_real_estate_class('real-estate-metabox');
    }

    /**
     * Enqueue admin script
     *
     * @since 1.0
     * @param string $hook
     * @return void
     */
    public function admin_scripts($hook) {
        $screen = get_current_screen();
        
        $min = WP_DEBUG ? '' : '.min';

        global $tpfw_registered_fields;

        /**
         * Init register nav menu meta item
         */
        if ( !empty( $tpfw_registered_fields ) ) {

            $tpfw_registered_fields = array_unique( $tpfw_registered_fields );

            wp_enqueue_style( 'font-awesome', TPFW_URL . 'assets/css/font-awesome' . $min . '.css', null, '4.7.0' );
            wp_enqueue_style( 'tpfw-admin', TPFW_URL . 'assets/css/admin' . $min . '.css', null, TPFW_VERSION );

            wp_enqueue_script( 'tpfw-libs', TPFW_URL . 'assets/js/libs' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
            wp_enqueue_script( 'tpfw-admin', TPFW_URL . 'assets/js/admin_fields' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
            wp_enqueue_script( 'dependency', TPFW_URL . 'assets/vendors/dependency/dependency' . $min . '.js', array( 'jquery' ), TPFW_VERSION );

            $upload_dir = wp_upload_dir();

            $localize = array(
                'upload_url' => $upload_dir['baseurl']
            );

            foreach ( $tpfw_registered_fields as $type ) {
                switch ( $type ) {
                    case 'color_picker':
                        wp_enqueue_script( 'wp-color-picker' );
                        wp_enqueue_style( 'wp-color-picker' );
                        break;
                    case 'image_picker';
                    case 'upload';
                        $localize['upload_invalid_mime'] = esc_html__( 'The selected file has an invalid mimetype in this field.', 'tp-framework' );
                        wp_enqueue_media();
                        wp_enqueue_script( 'jquery-ui' );
                        break;
                    case 'icon_picker':
                        wp_enqueue_script( 'font-iconpicker', TPFW_URL . 'assets/vendors/fonticonpicker/js/jquery.fonticonpicker' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
                        wp_enqueue_style( 'font-iconpicker', TPFW_URL . 'assets/vendors/fonticonpicker/css/jquery.fonticonpicker' . $min . '.css', null, TPFW_VERSION );
                        break;
                    case 'map':
                        $gmap_key = sanitize_text_field( apply_filters( 'tpfw_gmap_key', '' ) );
                        wp_enqueue_script( 'google-map-v-3', "//maps.googleapis.com/maps/api/js?v=3&libraries=places&key={$gmap_key}", array( 'jquery' ), null, true );
                        wp_enqueue_script( 'geocomplete', TPFW_URL . 'assets/vendors/geocomplete/jquery.geocomplete' . $min . '.js', null, TPFW_VERSION );
                        break;
                    case 'icon_picker':
                        wp_enqueue_script( 'font-iconpicker', TPFW_URL . 'assets/vendors/fonticonpicker/js/jquery.fonticonpicker' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
                        wp_enqueue_style( 'font-iconpicker', TPFW_URL . 'assets/vendors/fonticonpicker/css/jquery.fonticonpicker' . $min . '.css', null, TPFW_VERSION );
                        break;
                    case 'link':

                        $screens = apply_filters( 'tpfw_link_on_screens', array( 'post.php', 'post-new.php' ) );
                        if ( !in_array( $hook_suffix, $screens ) ) {
                            wp_enqueue_style( 'editor-buttons' );
                            wp_enqueue_script( 'wplink' );

                            add_action( 'in_admin_header', 'tpfw_link_editor_hidden' );
                            add_action( 'customize_controls_print_footer_scripts', 'tpfw_link_editor_hidden' );
                        }
                        break;
                    case 'repeater':
                        wp_enqueue_script( 'jquery-repeater', TPFW_URL . 'assets/js/repeater-libs' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
                        break;
                    case 'select':
                    case 'typography':
                    case 'autocomplete':

                        wp_enqueue_script( 'selectize', TPFW_URL . 'assets/vendors/selectize/selectize' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
                        wp_enqueue_style( 'selectize', TPFW_URL . 'assets/vendors/selectize/selectize' . $min . '.css', null, TPFW_VERSION );
                        wp_enqueue_style( 'selectize-skin', TPFW_URL . 'assets/vendors/selectize/selectize.default' . $min . '.css', null, TPFW_VERSION );

                        if ( $type == 'typography' ) {

                            $localize['subsets'] = Tpfw_Fonts::get_google_font_subsets();

                            $localize['variants'] = Tpfw_Fonts::get_all_variants();

                            $localize['fonts'] = TPFW_Fonts::get_all_fonts_reordered();
                        }

                        break;
                    case 'datetime':
                        wp_enqueue_script( 'datetimepicker', TPFW_URL . 'assets/vendors/datetimepicker/jquery.datetimepicker.full' . $min . '.js', array( 'jquery' ), TPFW_VERSION );
                        wp_enqueue_style( 'datetimepicker', TPFW_URL . 'assets/vendors/datetimepicker/jquery.datetimepicker' . $min . '.css', null, TPFW_VERSION );
                        break;
                    default :
                        do_action( 'tpfw_admin_' . $type . '_scripts' );
                        break;
                }
            }

            wp_localize_script( 'tpfw-admin', 'tpfw_var', apply_filters( 'tpfw_localize_var', $localize ) );
        }
    }

    /**
     * Load local files.
     *
     * @since 1.0
     * @return void
     */
    public function load_plugin_textdomain() {

        // Set filter for plugin's languages directory
        $dir = WP_REAL_ESTATE_DIR . 'languages/';
        $dir = apply_filters('wp_real_estate_languages_directory', $dir);

        // Traditional WordPress plugin locale filter
        $locale = apply_filters('plugin_locale', get_locale(), 'wp-real-estate');
        $mofile = sprintf('%1$s-%2$s.mo', 'wp-real-estate', $locale);

        // Setup paths to current locale file
        $mofile_local = $dir . $mofile;

        $mofile_global = WP_LANG_DIR . '/wp-real-estate/' . $mofile;

        if (file_exists($mofile_global)) {
            // Look in global /wp-content/languages/tp-image-optimizer folder
            load_textdomain('wp-real-estate', $mofile_global);
        } elseif (file_exists($mofile_local)) {
            // Look in local /wp-content/plugins/wp-real-estate/languages/ folder
            load_textdomain('wp-real-estate', $mofile_local);
        } else {
            // Load the default language files
            load_plugin_textdomain('wp-real-estate', false, $dir);
        }
    }

    /**
     * Hook
     *
     * @since 1.0.0
     */
    private function hook() {
        // add_action('admin_menu', array($this, 'register_page'));
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        register_activation_hook(__FILE__, array($this, 'install'));
        register_deactivation_hook(__FILE__, array($this, 'uninstall'));

        if ( is_admin() )  {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            add_action( 'admin_init', array( $this, 'admin_fields' ) );

            add_action( 'load-post.php', array( $this, 'metabox_init' ) );
            add_action( 'load-post-new.php', array( $this, 'metabox_init' ) );
        }

        

    }

    /**
     * Load field in admin
     * @since 1.0
     */
    public function admin_fields() {
        include TPFW_DIR . 'includes/admin-fields/field_default.php';
        // include TPFW_DIR . 'includes/admin-fields/field_color_picker.php';
        // include TPFW_DIR . 'includes/admin-fields/field_datetime.php';
        // include TPFW_DIR . 'includes/admin-fields/field_icon_picker.php';
        // include TPFW_DIR . 'includes/admin-fields/field_image_picker.php';
        // include TPFW_DIR . 'includes/admin-fields/field_image_select.php';
        // include TPFW_DIR . 'includes/admin-fields/field_link.php';
        // include TPFW_DIR . 'includes/admin-fields/field_map.php';
        // include TPFW_DIR . 'includes/admin-fields/field_repeater.php';
        // include TPFW_DIR . 'includes/admin-fields/field_typography.php';
        // include TPFW_DIR . 'includes/admin-fields/field_autocomplete.php';
        // include TPFW_DIR . 'includes/admin-fields/field_upload.php';
    }
    

    /**
     * Uninstall plugin
     *
     * @global type $wpdb
     */
    public function uninstall() {

    }

    /**
     * Install plugin
     *
     * @since 1.0.0
     */
    function install() {
        // $table = new TP_Image_Optimizer_Table();
        // $table->create(); // Create data table

        // if (!get_option('tp_image_optimizer_installed')) {
        //     add_option('tp_image_optimizer_installed', 'false', '', 'yes');
        // }

        // // Error option
        // if (!get_option('tpio_error_count')) {
        //     add_option('tpio_error_count', 0, '', 'yes');
        // }

        // // Select optimize all size
        // $all_size = get_intermediate_image_sizes();
        // array_push($all_size, 'full');
        // $all_size = implode(',', $all_size);
        // update_option('tp_image_optimizer_sizes', $all_size);

        // // Compress option
        // update_option('tp_image_optimizer_compress_level', 3);
    }

}

/**
 * Main instance of TP Framework.
 *
 * Returns the main instance of TP Framework to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return TP Framework
 */
function wp_real_estate() {
    return WP_Real_Estate::instance();
}

// Global for backwards compatibility.
$GLOBALS['WP_Real_Estate'] = wp_real_estate();

require 'sample/post-meta.php';
