<?php
/*
 * Plugin Name: Maintenance Mode
 * Plugin URI: https://github.com/Kilian-Schwarz/WordPress-maintenance-mode
 * Description: Displays a customizable Maintenance Mode page with advanced features.
 * Version: 3.2.0
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
    wp_enqueue_style('mm-admin-style', MM_PLUGIN_URL . 'assets/css/admin.css', array(), '1.2');
    wp_enqueue_script('mm-admin-script', MM_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker', 'chartjs'), '1.2', true);

    // Enqueue Chart.js for statistics
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array('jquery'), '3.5.1', true);

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
    wp_enqueue_script('mm-frontend-script', MM_PLUGIN_URL . 'assets/js/maintenance.js', array('jquery'), '1.2', true);

    // Pass custom JS to frontend
    $custom_js = get_option('mm_custom_js', '');
    wp_localize_script('mm-frontend-script', 'mmCustomJs', $custom_js);
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
        'mm_show_animation' => isset($_POST['mm_show_animation']) ? 1 : 0,
        // Timer settings
        'mm_enable_timer' => isset($_POST['mm_enable_timer']) ? 1 : 0,
        'mm_timer_end_date' => isset($_POST['mm_timer_end_date']) ? sanitize_text_field($_POST['mm_timer_end_date']) : get_option('mm_timer_end_date'),
        'mm_auto_disable' => isset($_POST['mm_auto_disable']) ? 1 : 0,
        // Schedule settings
        'mm_enable_schedule' => isset($_POST['mm_enable_schedule']) ? 1 : 0,
        'mm_schedule_start' => isset($_POST['mm_schedule_start']) ? sanitize_text_field($_POST['mm_schedule_start']) : get_option('mm_schedule_start'),
        'mm_schedule_end' => isset($_POST['mm_schedule_end']) ? sanitize_text_field($_POST['mm_schedule_end']) : get_option('mm_schedule_end'),
        // Design settings
        'mm_logo_image_id' => isset($_POST['mm_logo_image_id']) ? intval($_POST['mm_logo_image_id']) : get_option('mm_logo_image_id'),
        'mm_favicon_image_id' => isset($_POST['mm_favicon_image_id']) ? intval($_POST['mm_favicon_image_id']) : get_option('mm_favicon_image_id'),
        'mm_background_video_url' => isset($_POST['mm_background_video_url']) ? esc_url_raw($_POST['mm_background_video_url']) : get_option('mm_background_video_url'),
        // Advanced settings
        'mm_custom_html' => isset($_POST['mm_custom_html']) ? wp_kses_post($_POST['mm_custom_html']) : get_option('mm_custom_html'),
        'mm_custom_css' => isset($_POST['mm_custom_css']) ? wp_strip_all_tags($_POST['mm_custom_css']) : get_option('mm_custom_css'),
        'mm_custom_js' => isset($_POST['mm_custom_js']) ? wp_strip_all_tags($_POST['mm_custom_js']) : get_option('mm_custom_js'),
        'mm_social_links' => isset($_POST['mm_social_links']) ? array_map('esc_url_raw', $_POST['mm_social_links']) : get_option('mm_social_links', array()),
        'mm_show_visitor_count' => isset($_POST['mm_show_visitor_count']) ? 1 : get_option('mm_show_visitor_count', 0),
        // SEO settings
        'mm_seo_meta_description' => isset($_POST['mm_seo_meta_description']) ? sanitize_text_field($_POST['mm_seo_meta_description']) : get_option('mm_seo_meta_description'),
        'mm_seo_meta_keywords' => isset($_POST['mm_seo_meta_keywords']) ? sanitize_text_field($_POST['mm_seo_meta_keywords']) : get_option('mm_seo_meta_keywords'),
        'mm_seo_robots' => isset($_POST['mm_seo_robots']) ? sanitize_text_field($_POST['mm_seo_robots']) : get_option('mm_seo_robots'),
        // Google Analytics
        'mm_google_analytics_id' => isset($_POST['mm_google_analytics_id']) ? sanitize_text_field($_POST['mm_google_analytics_id']) : get_option('mm_google_analytics_id'),
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

    $visitor_log = get_option('mm_visitor_log', array());
    $date = current_time('Y-m-d');
    if (isset($visitor_log[$date])) {
        $visitor_log[$date]++;
    } else {
        $visitor_log[$date] = 1;
    }
    update_option('mm_visitor_log', $visitor_log);
}
add_action('template_redirect', 'mm_increment_visitor_count');

// AJAX handler to reset statistics
function mm_reset_statistics() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 401);
    }
    delete_option('mm_visitor_log');
    wp_send_json_success();
}
add_action('wp_ajax_mm_reset_statistics', 'mm_reset_statistics');

// AJAX handler to download statistics
function mm_download_statistics() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized', 'Error', array('response' => 401));
    }

    $visitor_log = get_option('mm_visitor_log', array());
    $csv_data = "Datum,Besucher\n";
    foreach ($visitor_log as $date => $count) {
        $csv_data .= "$date,$count\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="visitor_statistics.csv"');
    echo $csv_data;
    exit();
}
add_action('wp_ajax_mm_download_statistics', 'mm_download_statistics');