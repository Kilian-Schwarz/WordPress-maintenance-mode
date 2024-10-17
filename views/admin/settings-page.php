<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
function mm_add_admin_menu() {
    add_menu_page(
        'Maintenance Mode',
        'Maintenance Mode',
        'manage_options',
        'maintenance-mode',
        'mm_render_settings_page',
        'dashicons-hammer', // Custom icon
        80
    );
}
add_action('admin_menu', 'mm_add_admin_menu');

// Register settings
function mm_register_settings() {
    // Register settings
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
}
add_action('admin_init', 'mm_register_settings');

// Render settings page
function mm_render_settings_page() {
    ?>
    <div class="mm-wrap">
        <h1>Maintenance Mode Einstellungen</h1>
        <nav class="mm-nav-tab-wrapper">
            <a href="#mm-tab-general" class="mm-nav-tab mm-nav-tab-active">Allgemein</a>
            <a href="#mm-tab-design" class="mm-nav-tab">Design</a>
            <a href="#mm-tab-advanced" class="mm-nav-tab">Erweitert</a>
        </nav>
        <form method="post" action="options.php">
            <?php
            settings_fields('mm_settings_group');
            ?>
            <div id="mm-tab-general" class="mm-tab-content mm-active">
                <table class="form-table">
                    <!-- General Settings Fields -->
                    <tr>
                        <th scope="row">Maintenance Mode aktivieren</th>
                        <td>
                            <label>
                                <input type="checkbox" name="mm_active" value="1" <?php checked(1, get_option('mm_active'), true); ?> />
                                Aktivieren
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Angezeigter Text</th>
                        <td>
                            <input type="text" name="mm_text" value="<?php echo esc_attr(get_option('mm_text', 'Wir sind bald zurück!')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Countdown-Timer aktivieren</th>
                        <td>
                            <label>
                                <input type="checkbox" name="mm_enable_timer" value="1" <?php checked(1, get_option('mm_enable_timer'), true); ?> />
                                Aktivieren
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enddatum des Timers</th>
                        <td>
                            <input type="datetime-local" name="mm_timer_end_date" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime(get_option('mm_timer_end_date', date('Y-m-d H:i:s'))))); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Automatische Deaktivierung nach Timer</th>
                        <td>
                            <label>
                                <input type="checkbox" name="mm_auto_disable" value="1" <?php checked(1, get_option('mm_auto_disable'), true); ?> />
                                Aktivieren
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="mm-tab-design" class="mm-tab-content">
                <table class="form-table">
                    <!-- Design Settings Fields -->
                    <tr>
                        <th scope="row">Hintergrundbild</th>
                        <td>
                            <?php $image_id = get_option('mm_background_image_id'); ?>
                            <div>
                                <img id="mm_background_image_preview" src="<?php echo esc_url($image_id ? wp_get_attachment_url($image_id) : ''); ?>" style="max-width: 300px; max-height: 150px; display: <?php echo $image_id ? 'block' : 'none'; ?>;" />
                                <input type="hidden" name="mm_background_image_id" id="mm_background_image_id" value="<?php echo esc_attr($image_id); ?>" />
                                <button type="button" class="button mm-upload-button" data-target="mm_background_image_id">Bild auswählen</button>
                                <button type="button" class="button mm-remove-button" data-target="mm_background_image_id" style="display: <?php echo $image_id ? 'inline-block' : 'none'; ?>;">Bild entfernen</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Hintergrundfarbe</th>
                        <td>
                            <input type="text" name="mm_background_color" value="<?php echo esc_attr(get_option('mm_background_color', '#131313')); ?>" class="mm-color-field" data-default-color="#131313" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Logo</th>
                        <td>
                            <?php $logo_id = get_option('mm_logo_image_id'); ?>
                            <div>
                                <img id="mm_logo_image_preview" src="<?php echo esc_url($logo_id ? wp_get_attachment_url($logo_id) : ''); ?>" style="max-width: 200px; max-height: 100px; display: <?php echo $logo_id ? 'block' : 'none'; ?>;" />
                                <input type="hidden" name="mm_logo_image_id" id="mm_logo_image_id" value="<?php echo esc_attr($logo_id); ?>" />
                                <button type="button" class="button mm-upload-button" data-target="mm_logo_image_id">Logo auswählen</button>
                                <button type="button" class="button mm-remove-button" data-target="mm_logo_image_id" style="display: <?php echo $logo_id ? 'inline-block' : 'none'; ?>;">Logo entfernen</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Glitch-Effekt aktivieren</th>
                        <td>
                            <label>
                                <input type="checkbox" name="mm_enable_glitch" value="1" <?php checked(1, get_option('mm_enable_glitch'), true); ?> />
                                Aktivieren
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Eigenes HTML</th>
                        <td>
                            <textarea name="mm_custom_html" rows="5" class="large-text code"><?php echo esc_textarea(get_option('mm_custom_html', '')); ?></textarea>
                            <p class="description">Hier kannst du eigenes HTML hinzufügen, das auf der Wartungsseite angezeigt wird.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Eigenes CSS</th>
                        <td>
                            <textarea name="mm_custom_css" rows="5" class="large-text code"><?php echo esc_textarea(get_option('mm_custom_css', '')); ?></textarea>
                            <p class="description">Hier kannst du eigenes CSS hinzufügen, um die Wartungsseite zu stylen.</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="mm-tab-advanced" class="mm-tab-content">
                <table class="form-table">
                    <!-- Advanced Settings Fields -->
                    <tr>
                        <th scope="row">IP-Whitelist</th>
                        <td>
                            <textarea name="mm_ip_whitelist" rows="3" class="large-text code"><?php echo esc_textarea(get_option('mm_ip_whitelist', '')); ?></textarea>
                            <p class="description">Gib hier IP-Adressen ein, die den Maintenance Mode umgehen können (eine pro Zeile).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Soziale Medien Links</th>
                        <td>
                            <?php
                            $social_links = get_option('mm_social_links', array());
                            $platforms = array('Facebook', 'Twitter', 'Instagram', 'LinkedIn', 'YouTube');
                            foreach ($platforms as $platform) {
                                $url = isset($social_links[strtolower($platform)]) ? $social_links[strtolower($platform)] : '';
                                echo "<p><label>{$platform} URL: <input type='url' name='mm_social_links[" . strtolower($platform) . "]' value='" . esc_attr($url) . "' class='regular-text' /></label></p>";
                            }
                            ?>
                            <p class="description">Füge Links zu deinen Social Media Profilen hinzu.</p>
                        </td>
                    </tr>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}