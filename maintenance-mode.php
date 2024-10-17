<?php
/*
 * Plugin Name: Maintenance Mode
 * Plugin URI: https://github.com/Kilian-Schwarz/WordPress-maintenance-mode
 * Description: Displays a customizable Maintenance Mode page with advanced features.
 * Version: 3.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Tested up to: 6.3
 * Author: Kilian Schwarz
 * Author URI: https://github.com/Kilian-Schwarz
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: maintenance-mode
 * Domain Path: /languages
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

// Enqueue frontend scripts and styles
function mm_enqueue_frontend_scripts() {
    if (!get_option('mm_active')) {
        return;
    }
    wp_enqueue_script('mm-frontend-script', MM_PLUGIN_URL . 'assets/js/maintenance.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'mm_enqueue_frontend_scripts');

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
    $current_time = current_time('timestamp');

    // Auto-disable after timer ends
    if ($current_time > $timer_end_date && get_option('mm_auto_disable')) {
        update_option('mm_active', 0);
        return;
    }

    // Schedule maintenance mode
    $schedule_start = strtotime(get_option('mm_schedule_start'));
    $schedule_end = strtotime(get_option('mm_schedule_end'));
    if (get_option('mm_enable_schedule') && ($current_time < $schedule_start || $current_time > $schedule_end)) {
        return;
    }

    if ($pagenow !== 'wp-login.php' && !current_user_can('manage_options') && !is_admin()) {
        $http_status = get_option('mm_http_status_code', 503);
        status_header($http_status);
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
        // Existing options...
        'mm_custom_js' => isset($_POST['mm_custom_js']) ? wp_strip_all_tags($_POST['mm_custom_js']) : get_option('mm_custom_js'),
        'mm_http_status_code' => isset($_POST['mm_http_status_code']) ? intval($_POST['mm_http_status_code']) : get_option('mm_http_status_code'),
        'mm_seo_meta_description' => isset($_POST['mm_seo_meta_description']) ? sanitize_text_field($_POST['mm_seo_meta_description']) : get_option('mm_seo_meta_description'),
        'mm_seo_meta_keywords' => isset($_POST['mm_seo_meta_keywords']) ? sanitize_text_field($_POST['mm_seo_meta_keywords']) : get_option('mm_seo_meta_keywords'),
        'mm_seo_robots' => isset($_POST['mm_seo_robots']) ? sanitize_text_field($_POST['mm_seo_robots']) : get_option('mm_seo_robots'),
        'mm_google_analytics_id' => isset($_POST['mm_google_analytics_id']) ? sanitize_text_field($_POST['mm_google_analytics_id']) : get_option('mm_google_analytics_id'),
        'mm_background_video_url' => isset($_POST['mm_background_video_url']) ? esc_url_raw($_POST['mm_background_video_url']) : get_option('mm_background_video_url'),
        'mm_enable_schedule' => isset($_POST['mm_enable_schedule']) ? 1 : 0,
        'mm_schedule_start' => isset($_POST['mm_schedule_start']) ? sanitize_text_field($_POST['mm_schedule_start']) : get_option('mm_schedule_start'),
        'mm_schedule_end' => isset($_POST['mm_schedule_end']) ? sanitize_text_field($_POST['mm_schedule_end']) : get_option('mm_schedule_end'),
        'mm_visitor_count' => get_option('mm_visitor_count', 0), // Read-only
        // Add other options as needed
    );

    // Include the maintenance page template with overridden options
    include MM_PLUGIN_DIR . 'views/maintenance-preview.php';

    exit();
}
add_action('wp_ajax_mm_preview', 'mm_ajax_preview');

// Increment visitor count
function mm_increment_visitor_count() {
    if (!get_option('mm_active')) {
        return;
    }

    $visitor_count = get_option('mm_visitor_count', 0);
    update_option('mm_visitor_count', $visitor_count + 1);
}
add_action('template_redirect', 'mm_increment_visitor_count');

// Clear visitor count on deactivation
function mm_clear_visitor_count() {
    delete_option('mm_visitor_count');
}
register_deactivation_hook(__FILE__, 'mm_clear_visitor_count');