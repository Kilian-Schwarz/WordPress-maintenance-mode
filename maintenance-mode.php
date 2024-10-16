<?php
/*
 * Plugin Name: Maintenance Mode
 * Plugin URI: https://github.com/Kilian-Schwarz/WordPress-maintenance-mode
 * Description: Displays a customizable Maintenance Mode page.
 * Version: 2.1
 * Author: Kilian Schwarz
 * Author URI: https://github.com/Kilian-Schwarz
 * License: GPL-3.0
 */

/**
 * Hinzufügen der Einstellungen zur WordPress-Admin-Seite
 */
function mm_add_settings_page() {
    add_options_page(
        'Maintenance Mode Settings',
        'Maintenance Mode',
        'manage_options',
        'maintenance-mode-settings',
        'mm_render_settings_page'
    );
}
add_action('admin_menu', 'mm_add_settings_page');

/**
 * Registrierung der Einstellungen und Felder
 */
function mm_register_settings() {
    // Registrierung der Einstellungen
    register_setting('mm_settings_group', 'mm_active');
    register_setting('mm_settings_group', 'mm_text');
    register_setting('mm_settings_group', 'mm_background_image_id');
    register_setting('mm_settings_group', 'mm_background_color');
    register_setting('mm_settings_group', 'mm_enable_glitch');
    register_setting('mm_settings_group', 'mm_enable_timer');
    register_setting('mm_settings_group', 'mm_timer_end_date');
    register_setting('mm_settings_group', 'mm_auto_disable');
    register_setting('mm_settings_group', 'mm_custom_css');
    register_setting('mm_settings_group', 'mm_custom_html');
    register_setting('mm_settings_group', 'mm_ip_whitelist');
    register_setting('mm_settings_group', 'mm_social_links');
    register_setting('mm_settings_group', 'mm_logo_image_id');

    // Hinzufügen der Einstellungen
    add_settings_section('mm_main_section', 'Haupteinstellungen', null, 'maintenance-mode-settings');

    add_settings_field('mm_active', 'Maintenance Mode aktivieren', 'mm_active_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_text', 'Angezeigter Text', 'mm_text_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_background_image', 'Hintergrundbild', 'mm_background_image_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_background_color', 'Hintergrundfarbe', 'mm_background_color_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_logo_image', 'Logo', 'mm_logo_image_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_enable_glitch', 'Glitch-Effekt aktivieren', 'mm_enable_glitch_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_enable_timer', 'Countdown-Timer aktivieren', 'mm_enable_timer_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_timer_end_date', 'Enddatum des Timers', 'mm_timer_end_date_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_auto_disable', 'Automatische Deaktivierung nach Timer', 'mm_auto_disable_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_custom_html', 'Eigenes HTML', 'mm_custom_html_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_custom_css', 'Eigenes CSS', 'mm_custom_css_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_ip_whitelist', 'IP-Whitelist', 'mm_ip_whitelist_callback', 'maintenance-mode-settings', 'mm_main_section');
    add_settings_field('mm_social_links', 'Soziale Medien Links', 'mm_social_links_callback', 'maintenance-mode-settings', 'mm_main_section');
}
add_action('admin_init', 'mm_register_settings');

/**
 * Rendern der Einstellungen-Seite
 */
function mm_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Maintenance Mode Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mm_settings_group');
            do_settings_sections('maintenance-mode-settings');
            submit_button();
            ?>
        </form>
        <div style="margin-top: 20px; padding: 10px; background: #f1f1f1;">
            <p>Plugin entwickelt von <a href="https://github.com/Kilian-Schwarz" target="_blank">Kilian Schwarz</a>.</p>
            <p>Lizenz: <a href="https://www.gnu.org/licenses/gpl-3.0.de.html" target="_blank">GPL-3.0</a></p>
        </div>
    </div>
    <?php
}

/**
 * Callback-Funktionen für die Einstellungen
 */
function mm_active_callback() {
    $active = get_option('mm_active', 0);
    echo "<input type='checkbox' name='mm_active' value='1' " . checked(1, $active, false) . " />";
}

function mm_text_callback() {
    $text = get_option('mm_text', 'Wir sind bald zurück!');
    echo "<input type='text' name='mm_text' value='" . esc_attr($text) . "' class='regular-text' />";
}

function mm_background_image_callback() {
    $image_id = get_option('mm_background_image_id');
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <div>
        <img id="mm_background_image_preview" src="<?php echo esc_url($image_url); ?>" style="max-width: 300px; max-height: 150px; display: <?php echo $image_url ? 'block' : 'none'; ?>;" />
        <input type="hidden" name="mm_background_image_id" id="mm_background_image_id" value="<?php echo esc_attr($image_id); ?>" />
        <button type="button" class="button" id="mm_upload_background_image_button">Bild auswählen</button>
        <button type="button" class="button" id="mm_remove_background_image_button" style="display: <?php echo $image_url ? 'inline-block' : 'none'; ?>;">Bild entfernen</button>
    </div>
    <?php
}

function mm_background_color_callback() {
    $bg_color = get_option('mm_background_color', '#131313');
    echo "<input type='text' name='mm_background_color' value='" . esc_attr($bg_color) . "' class='mm-color-field' data-default-color='#131313' />";
}

function mm_logo_image_callback() {
    $logo_id = get_option('mm_logo_image_id');
    $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
    ?>
    <div>
        <img id="mm_logo_image_preview" src="<?php echo esc_url($logo_url); ?>" style="max-width: 200px; max-height: 100px; display: <?php echo $logo_url ? 'block' : 'none'; ?>;" />
        <input type="hidden" name="mm_logo_image_id" id="mm_logo_image_id" value="<?php echo esc_attr($logo_id); ?>" />
        <button type="button" class="button" id="mm_upload_logo_image_button">Logo auswählen</button>
        <button type="button" class="button" id="mm_remove_logo_image_button" style="display: <?php echo $logo_url ? 'inline-block' : 'none'; ?>;">Logo entfernen</button>
    </div>
    <?php
}

function mm_enable_glitch_callback() {
    $enable_glitch = get_option('mm_enable_glitch', 0);
    echo "<input type='checkbox' name='mm_enable_glitch' value='1' " . checked(1, $enable_glitch, false) . " />";
}

function mm_enable_timer_callback() {
    $enable_timer = get_option('mm_enable_timer', 0);
    echo "<input type='checkbox' name='mm_enable_timer' value='1' " . checked(1, $enable_timer, false) . " />";
}

function mm_timer_end_date_callback() {
    $timer_end_date = get_option('mm_timer_end_date', date('Y-m-d H:i:s'));
    echo "<input type='datetime-local' name='mm_timer_end_date' value='" . esc_attr(date('Y-m-d\TH:i', strtotime($timer_end_date))) . "' />";
}

function mm_auto_disable_callback() {
    $auto_disable = get_option('mm_auto_disable', 0);
    echo "<input type='checkbox' name='mm_auto_disable' value='1' " . checked(1, $auto_disable, false) . " />";
}

function mm_custom_html_callback() {
    $custom_html = get_option('mm_custom_html', '');
    echo "<textarea name='mm_custom_html' rows='5' class='large-text code'>" . esc_textarea($custom_html) . "</textarea>";
    echo "<p class='description'>Hier kannst du eigenes HTML hinzufügen, das auf der Wartungsseite angezeigt wird.</p>";
}

function mm_custom_css_callback() {
    $custom_css = get_option('mm_custom_css', '');
    echo "<textarea name='mm_custom_css' rows='5' class='large-text code'>" . esc_textarea($custom_css) . "</textarea>";
    echo "<p class='description'>Hier kannst du eigenes CSS hinzufügen, um die Wartungsseite zu stylen.</p>";
}

function mm_ip_whitelist_callback() {
    $ip_whitelist = get_option('mm_ip_whitelist', '');
    echo "<textarea name='mm_ip_whitelist' rows='3' class='large-text code'>" . esc_textarea($ip_whitelist) . "</textarea>";
    echo "<p class='description'>Gib hier IP-Adressen ein, die den Maintenance Mode umgehen können (eine pro Zeile).</p>";
}

function mm_social_links_callback() {
    $social_links = get_option('mm_social_links', array());
    $platforms = array('Facebook', 'Twitter', 'Instagram', 'LinkedIn', 'YouTube');
    foreach ($platforms as $platform) {
        $url = isset($social_links[strtolower($platform)]) ? $social_links[strtolower($platform)] : '';
        echo "<p><label>{$platform} URL: <input type='url' name='mm_social_links[" . strtolower($platform) . "]' value='" . esc_attr($url) . "' class='regular-text' /></label></p>";
    }
    echo "<p class='description'>Füge Links zu deinen Social Media Profilen hinzu.</p>";
}

/**
 * Enqueue von Scripts und Styles für die Admin-Seite
 */
function mm_admin_enqueue_scripts($hook_suffix) {
    if ($hook_suffix != 'settings_page_maintenance-mode-settings') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('mm-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), false, true);
    wp_enqueue_style('mm-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin.css');
}
add_action('admin_enqueue_scripts', 'mm_admin_enqueue_scripts');

/**
 * Maintenance Mode Logik
 */
function mm_maintenance_mode() {
    if (!get_option('mm_active')) {
        return; // Maintenance Mode nicht aktiv
    }

    $ip_whitelist = array_filter(array_map('trim', explode("\n", get_option('mm_ip_whitelist', ''))));
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Überprüfung der IP-Whitelist
    if (in_array($user_ip, $ip_whitelist)) {
        return;
    }

    global $pagenow;
    $timer_end_date = strtotime(get_option('mm_timer_end_date'));
    $current_time = time();

    if ($current_time > $timer_end_date && get_option('mm_auto_disable')) {
        update_option('mm_active', 0); // Automatische Deaktivierung des Maintenance Modes
        return;
    }

    if ($pagenow !== 'wp-login.php' && !current_user_can('manage_options') && !is_admin()) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable', true, 503);
        header('Content-Type: text/html; charset=utf-8');
        if (file_exists(plugin_dir_path(__FILE__) . 'views/maintenance.php')) {
            require_once(plugin_dir_path(__FILE__) . 'views/maintenance.php');
        }
        exit();
    }
}
add_action('template_redirect', 'mm_maintenance_mode');