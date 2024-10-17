<?php
/*
 * Plugin Name: Maintenance Mode
 * Plugin URI: https://github.com/Kilian-Schwarz/WordPress-maintenance-mode
 * Description: Displays a customizable Maintenance Mode page.
 * Version: 2.2
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
    wp_enqueue_style('mm-admin-style', MM_PLUGIN_URL . 'assets/css/admin.css');
    wp_enqueue_script('mm-admin-script', MM_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), false, true);
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