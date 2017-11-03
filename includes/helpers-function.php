<?php

/**
 * IO template
 * @param String $slug Slug namme of template
 * @param array $data Data use to get variable for template
 * @since 1.0.0
 */
function wp_real_estate_template($slug, $data = array()) {
    if (is_array($data)) {
        extract($data);
    }
    $template = apply_filters('wp_real_estate_template_path', get_template_directory() . '/wp_real_estate') . '/' . $slug . '.php';

    if (!file_exists($template)) {
        $template = WP_REAL_ESTATE_DIR . 'templates/' . $slug . '.php';
    }
    include $template;
}

/**
 * IO get template
 * @param String $slug Slug namme of template
 * @param array $data Data use to get variable for template
 * @return string Html template
 * @since 1.0.7
 */
function wp_real_estate_get_template($slug, $data = array()) {
    ob_start();
    wp_real_estate_template($slug, $data);
    return ob_get_clean();
}

/**
 * Call class
 * 
 * @since 1.0.0
 */
function wp_real_estate_class($name) {
    include WP_REAL_ESTATE_DIR . 'includes/class-' . $name . '.php';
}

/**
 * Insert WordPress Table
 * @param string $name of table
 * @since 1.0.0
 */
function wp_real_estate_table($name) {
    include WP_REAL_ESTATE_DIR . 'includes/table/class-table-' . $name . '.php';
}


/**
 * Show message
 * 
 * @param string $notify  - Content of notify
 * @param boolean $boolean - TRUE mean as success and FALSE 
 * @since 1.0.0
 */
function wp_real_estate_notify($notify, $bool) {
    $message = $notify;
    $class   = "";
    if (!$bool) {
        $class = 'notice notice-error';
    } else {
        $class = 'notice notice-success';
    }
    printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
}

/**
 * Check IsJSON
 * 
 * 
 * @return boolean 
 * @since 1.0.0
 */
function isJSON($string) {
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}