<?php
/*
 * Plugin Name: Maintenance Mode
 * Plugin URI: https://github.com/Kilian-Schwarz/WordPress-maintenance-mode
 * Description: Displays a customizable Maintenance Mode page.
 * Version: 2.6
 * Author: Kilian Schwarz
 * Author URI: https://github.com/Kilian-Schwarz
 * License: GPL-3.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin paths
define('MM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include admin settings
require_once MM_PLUGIN_DIR . 'views/admin/settings-page.php';

// Enqueue admin scripts and styles
function mm_admin_enqueue_scripts($hook_suffix) {
    if ($hook_suffix != 'toplevel_page_maintenance-mode') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('mm-admin-style', MM_PLUGIN_URL . 'assets/css/admin.css', array(), '1.0');
    wp_enqueue_script('mm-admin-script', MM_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), '1.0', true);

    // Localize script for AJAX
    wp_localize_script('mm-admin-script', 'mmAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_icon_url' => esc_url(get_site_icon_url(32)),
    ));
}
add_action('admin_enqueue_scripts', 'mm_admin_enqueue_scripts');

// Maintenance Mode Logic
function mm_maintenance_mode() {
    if (!get_option('mm_active')) {
        return;
    }

    $ip_whitelist = array_filter(array_map('trim', explode("\n", get_option('mm_ip_whitelist', ''))));
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if (in_array($user_ip, $ip_whitelist)) {
        return;
    }

    global $pagenow;
    $timer_end_date = strtotime(get_option('mm_timer_end_date'));
    $current_time = time();

    if ($current_time > $timer_end_date && get_option('mm_auto_disable')) {
        update_option('mm_active', 0);
        return;
    }

    if ($pagenow !== 'wp-login.php' && !current_user_can('manage_options') && !is_admin()) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable', true, 503);
        header('Content-Type: text/html; charset=utf-8');
        require_once MM_PLUGIN_DIR . 'views/maintenance.php';
        exit();
    }
}
add_action('template_redirect', 'mm_maintenance_mode');

// AJAX handler for live preview
function mm_ajax_preview() {
    // Temporarily override options with $_POST data
    $options = array(
        'mm_text' => isset($_POST['mm_text']) ? sanitize_text_field($_POST['mm_text']) : get_option('mm_text'),
        'mm_background_image_id' => isset($_POST['mm_background_image_id']) ? intval($_POST['mm_background_image_id']) : get_option('mm_background_image_id'),
        'mm_background_color' => isset($_POST['mm_background_color']) ? sanitize_hex_color($_POST['mm_background_color']) : get_option('mm_background_color'),
        'mm_font_color' => isset($_POST['mm_font_color']) ? sanitize_hex_color($_POST['mm_font_color']) : get_option('mm_font_color'),
        'mm_font_size' => isset($_POST['mm_font_size']) ? intval($_POST['mm_font_size']) : get_option('mm_font_size'),
        'mm_font_bold' => isset($_POST['mm_font_bold']) ? 1 : 0,
        'mm_font_italic' => isset($_POST['mm_font_italic']) ? 1 : 0,
        'mm_font_underline' => isset($_POST['mm_font_underline']) ? 1 : 0,
        'mm_font_strikethrough' => isset($_POST['mm_font_strikethrough']) ? 1 : 0,
        'mm_enable_glitch' => isset($_POST['mm_enable_glitch']) ? 1 : 0,
        'mm_enable_timer' => isset($_POST['mm_enable_timer']) ? 1 : 0,
        'mm_timer_end_date' => isset($_POST['mm_timer_end_date']) ? sanitize_text_field($_POST['mm_timer_end_date']) : get_option('mm_timer_end_date'),
        'mm_logo_image_id' => isset($_POST['mm_logo_image_id']) ? intval($_POST['mm_logo_image_id']) : get_option('mm_logo_image_id'),
        'mm_favicon_image_id' => isset($_POST['mm_favicon_image_id']) ? intval($_POST['mm_favicon_image_id']) : get_option('mm_favicon_image_id'),
        'mm_custom_html' => isset($_POST['mm_custom_html']) ? wp_kses_post($_POST['mm_custom_html']) : get_option('mm_custom_html'),
        'mm_custom_css' => isset($_POST['mm_custom_css']) ? wp_strip_all_tags($_POST['mm_custom_css']) : get_option('mm_custom_css'),
        // Add other options as needed
    );

    // Include the maintenance page template with overridden options
    include MM_PLUGIN_DIR . 'views/maintenance-preview.php';

    exit();
}
add_action('wp_ajax_mm_preview', 'mm_ajax_preview');