<?php
// views/admin/settings-page.php

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
        'dashicons-hammer',
        80
    );
}
add_action('admin_menu', 'mm_add_admin_menu');

// Register settings
function mm_register_settings() {
    // General settings
    register_setting('mm_settings_group', 'mm_active');
    register_setting('mm_settings_group', 'mm_text');
    register_setting('mm_settings_group', 'mm_logo_image_id');
    register_setting('mm_settings_group', 'mm_social_links');
    register_setting('mm_settings_group', 'mm_show_visitor_count');

    // Design settings
    register_setting('mm_settings_group', 'mm_background_image_id');
    register_setting('mm_settings_group', 'mm_background_color');
    register_setting('mm_settings_group', 'mm_font_color');
    register_setting('mm_settings_group', 'mm_font_size');
    register_setting('mm_settings_group', 'mm_font_bold');
    register_setting('mm_settings_group', 'mm_font_italic');
    register_setting('mm_settings_group', 'mm_font_underline');
    register_setting('mm_settings_group', 'mm_font_strikethrough');
    register_setting('mm_settings_group', 'mm_favicon_image_id');
    register_setting('mm_settings_group', 'mm_enable_glitch');
    register_setting('mm_settings_group', 'mm_show_animation');
    register_setting('mm_settings_group', 'mm_background_video_url');

    // Schedule settings
    register_setting('mm_settings_group', 'mm_enable_timer');
    register_setting('mm_settings_group', 'mm_timer_end_date');
    register_setting('mm_settings_group', 'mm_auto_disable');
    register_setting('mm_settings_group', 'mm_enable_schedule');
    register_setting('mm_settings_group', 'mm_schedule_start');
    register_setting('mm_settings_group', 'mm_schedule_end');

    // Advanced settings
    register_setting('mm_settings_group', 'mm_custom_css');
    register_setting('mm_settings_group', 'mm_custom_html');
    register_setting('mm_settings_group', 'mm_custom_js');
    register_setting('mm_settings_group', 'mm_http_status_code');
    register_setting('mm_settings_group', 'mm_ip_whitelist');

    // SEO settings
    register_setting('mm_settings_group', 'mm_seo_meta_description');
    register_setting('mm_settings_group', 'mm_seo_meta_keywords');
    register_setting('mm_settings_group', 'mm_seo_robots');

    // Analytics
    register_setting('mm_settings_group', 'mm_google_analytics_id');
}
add_action('admin_init', 'mm_register_settings');

// Render settings page
function mm_render_settings_page() {
    ?>
    <div class="mm-wrap">
        <h1>Maintenance Mode Einstellungen</h1>
        <div class="mm-admin-container">
            <div class="mm-settings">
                <nav class="mm-nav-tab-wrapper">
                    <a href="#mm-tab-general" class="mm-nav-tab mm-nav-tab-active">Allgemein</a>
                    <a href="#mm-tab-design" class="mm-nav-tab">Design</a>
                    <a href="#mm-tab-schedule" class="mm-nav-tab">Zeitplan</a>
                    <a href="#mm-tab-advanced" class="mm-nav-tab">Erweitert</a>
                    <a href="#mm-tab-seo" class="mm-nav-tab">SEO</a>
                    <a href="#mm-tab-stats" class="mm-nav-tab">Statistiken</a>
                </nav>
                <form method="post" action="options.php" id="mm-settings-form">
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
                                <th scope="row">Logo</th>
                                <td>
                                    <?php $logo_id = get_option('mm_logo_image_id'); ?>
                                    <div>
                                        <img id="mm_logo_image_id_preview" src="<?php echo esc_url($logo_id ? wp_get_attachment_url($logo_id) : ''); ?>" style="max-width: 200px; max-height: 100px; display: <?php echo $logo_id ? 'block' : 'none'; ?>;" />
                                        <input type="hidden" name="mm_logo_image_id" id="mm_logo_image_id" value="<?php echo esc_attr($logo_id); ?>" />
                                        <button type="button" class="button mm-upload-button" data-target="mm_logo_image_id">Logo auswählen</button>
                                        <button type="button" class="button mm-remove-button" data-target="mm_logo_image_id" style="display: <?php echo $logo_id ? 'inline-block' : 'none'; ?>;">Logo entfernen</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Links</th>
                                <td>
                                    <div id="mm-links-container">
                                        <?php
                                        $social_links = get_option('mm_social_links', array(''));
                                        if (empty($social_links)) {
                                            $social_links = array('');
                                        }
                                        foreach ($social_links as $index => $url) {
                                            ?>
                                            <div class="mm-link-field">
                                                <input type="url" name="mm_social_links[]" value="<?php echo esc_attr($url); ?>" class="regular-text" />
                                                <?php if ($index > 0): ?>
                                                    <button type="button" class="button mm-remove-link-button">Entfernen</button>
                                                <?php else: ?>
                                                    <button type="button" class="button mm-remove-link-button" style="visibility: hidden;">Entfernen</button>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <button type="button" class="button" id="mm-add-link-button">Link hinzufügen</button>
                                    <p class="description">Füge Links hinzu, die auf der Wartungsseite angezeigt werden. Wenn ein Feld leer ist, wird es nicht angezeigt.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Besucherzähler anzeigen</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="mm_show_visitor_count" value="1" <?php checked(1, get_option('mm_show_visitor_count'), true); ?> />
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
                                        <img id="mm_background_image_id_preview" src="<?php echo esc_url($image_id ? wp_get_attachment_url($image_id) : ''); ?>" style="max-width: 300px; max-height: 150px; display: <?php echo $image_id ? 'block' : 'none'; ?>;" />
                                        <input type="hidden" name="mm_background_image_id" id="mm_background_image_id" value="<?php echo esc_attr($image_id); ?>" />
                                        <button type="button" class="button mm-upload-button" data-target="mm_background_image_id">Bild auswählen</button>
                                        <button type="button" class="button mm-remove-button" data-target="mm_background_image_id" style="display: <?php echo $image_id ? 'inline-block' : 'none'; ?>;">Bild entfernen</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Hintergrundvideo URL</th>
                                <td>
                                    <input type="url" name="mm_background_video_url" value="<?php echo esc_attr(get_option('mm_background_video_url', '')); ?>" class="regular-text" />
                                    <p class="description">Füge die URL zu einem Hintergrundvideo hinzu (MP4, WebM, etc.).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Hintergrundfarbe</th>
                                <td>
                                    <input type="text" name="mm_background_color" value="<?php echo esc_attr(get_option('mm_background_color', '#131313')); ?>" class="mm-color-field" data-default-color="#131313" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Text Schriftfarbe</th>
                                <td>
                                    <input type="text" name="mm_font_color" value="<?php echo esc_attr(get_option('mm_font_color', '#ffffff')); ?>" class="mm-font-color-field" data-default-color="#ffffff" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Text Schriftgröße (px)</th>
                                <td>
                                    <input type="number" name="mm_font_size" value="<?php echo esc_attr(get_option('mm_font_size', '48')); ?>" class="small-text" min="10" max="100" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Text Stil</th>
                                <td>
                                    <label><input type="checkbox" name="mm_font_bold" value="1" <?php checked(1, get_option('mm_font_bold'), true); ?> /> Fett</label><br>
                                    <label><input type="checkbox" name="mm_font_italic" value="1" <?php checked(1, get_option('mm_font_italic'), true); ?> /> Kursiv</label><br>
                                    <label><input type="checkbox" name="mm_font_underline" value="1" <?php checked(1, get_option('mm_font_underline'), true); ?> /> Unterstrichen</label><br>
                                    <label><input type="checkbox" name="mm_font_strikethrough" value="1" <?php checked(1, get_option('mm_font_strikethrough'), true); ?> /> Durchgestrichen</label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Favicon</th>
                                <td>
                                    <?php $favicon_id = get_option('mm_favicon_image_id'); ?>
                                    <div>
                                        <img id="mm_favicon_image_id_preview" src="<?php echo esc_url($favicon_id ? wp_get_attachment_url($favicon_id) : ''); ?>" style="max-width: 32px; max-height: 32px; display: <?php echo $favicon_id ? 'block' : 'none'; ?>;" />
                                        <input type="hidden" name="mm_favicon_image_id" id="mm_favicon_image_id" value="<?php echo esc_attr($favicon_id); ?>" />
                                        <button type="button" class="button mm-upload-button" data-target="mm_favicon_image_id">Favicon auswählen</button>
                                        <button type="button" class="button mm-remove-button" data-target="mm_favicon_image_id" style="display: <?php echo $favicon_id ? 'inline-block' : 'none'; ?>;">Favicon entfernen</button>
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
                                <th scope="row">Animation aktivieren</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="mm_show_animation" value="1" <?php checked(1, get_option('mm_show_animation'), true); ?> />
                                        Aktivieren
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="mm-tab-schedule" class="mm-tab-content">
                        <table class="form-table">
                            <!-- Schedule Settings Fields -->
                            <tr>
                                <th scope="row">Zeitplanung aktivieren</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="mm_enable_schedule" value="1" <?php checked(1, get_option('mm_enable_schedule'), true); ?> />
                                        Aktivieren
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Startdatum und -zeit</th>
                                <td>
                                    <input type="datetime-local" name="mm_schedule_start" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime(get_option('mm_schedule_start', date('Y-m-d H:i:s'))))); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Enddatum und -zeit</th>
                                <td>
                                    <input type="datetime-local" name="mm_schedule_end" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime(get_option('mm_schedule_end', date('Y-m-d H:i:s', strtotime('+1 day')))))); ?>" />
                                </td>
                            </tr>
                            <!-- Timer Settings Fields -->
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
                                    <input type="datetime-local" name="mm_timer_end_date" value="<?php echo esc_attr(date('Y-m-d\TH:i', strtotime(get_option('mm_timer_end_date', date('Y-m-d H:i:s'))))); ?>" min="<?php echo date('Y-m-d\TH:i'); ?>" />
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
                    <div id="mm-tab-advanced" class="mm-tab-content">
                        <table class="form-table">
                            <!-- Advanced Settings Fields -->
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
                            <tr>
                                <th scope="row">Eigenes JavaScript</th>
                                <td>
                                    <textarea name="mm_custom_js" rows="5" class="large-text code"><?php echo esc_textarea(get_option('mm_custom_js', '')); ?></textarea>
                                    <p class="description">Hier kannst du eigenes JavaScript hinzufügen, um zusätzliche Funktionalitäten zu implementieren.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">IP-Whitelist</th>
                                <td>
                                    <textarea name="mm_ip_whitelist" rows="3" class="large-text code"><?php echo esc_textarea(get_option('mm_ip_whitelist', '')); ?></textarea>
                                    <p class="description">Gib hier IP-Adressen ein, die den Maintenance Mode umgehen können (eine pro Zeile).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">HTTP-Statuscode</th>
                                <td>
                                    <select name="mm_http_status_code">
                                        <option value="200" <?php selected(get_option('mm_http_status_code'), '200'); ?>>200 OK</option>
                                        <option value="503" <?php selected(get_option('mm_http_status_code'), '503'); ?>>503 Service Unavailable</option>
                                    </select>
                                    <p class="description">Wähle den HTTP-Statuscode, der zurückgegeben werden soll.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="mm-tab-seo" class="mm-tab-content">
                        <table class="form-table">
                            <!-- SEO Settings Fields -->
                            <tr>
                                <th scope="row">Meta Description</th>
                                <td>
                                    <textarea name="mm_seo_meta_description" rows="3" class="large-text code"><?php echo esc_textarea(get_option('mm_seo_meta_description', '')); ?></textarea>
                                    <p class="description">Füge eine Meta Description für bessere SEO hinzu.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Meta Keywords</th>
                                <td>
                                    <input type="text" name="mm_seo_meta_keywords" value="<?php echo esc_attr(get_option('mm_seo_meta_keywords', '')); ?>" class="regular-text" />
                                    <p class="description">Füge Meta Keywords für bessere SEO hinzu, getrennt durch Kommas.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Robots Meta Tag</th>
                                <td>
                                    <select name="mm_seo_robots">
                                        <option value="index, follow" <?php selected(get_option('mm_seo_robots'), 'index, follow'); ?>>Index, Follow</option>
                                        <option value="noindex, nofollow" <?php selected(get_option('mm_seo_robots'), 'noindex, nofollow'); ?>>Noindex, Nofollow</option>
                                    </select>
                                    <p class="description">Steuere, wie Suchmaschinen deine Seite während der Wartung crawlen sollen.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="mm-tab-stats" class="mm-tab-content">
                        <table class="form-table">
                            <!-- Statistics Fields -->
                            <tr>
                                <th scope="row">Besucherstatistiken</th>
                                <td>
                                    <canvas id="mm-stats-chart" class="mm-stats-chart"></canvas>
                                    <div class="mm-stats-buttons">
                                        <button type="button" class="button mm-download-stats-button" id="mm-download-stats-button">Statistiken herunterladen</button>
                                        <button type="button" class="button mm-reset-stats-button" id="mm-reset-stats-button">Statistiken zurücksetzen</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Google Analytics Tracking ID</th>
                                <td>
                                    <input type="text" name="mm_google_analytics_id" value="<?php echo esc_attr(get_option('mm_google_analytics_id', '')); ?>" class="regular-text" />
                                    <p class="description">Füge deine Google Analytics Tracking ID hinzu (z.B. UA-XXXXX-Y).</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php submit_button(); ?>
                </form>
            </div>
            <div class="mm-preview">
                <div class="mm-browser">
                    <div class="mm-browser-header">
                        <div class="mm-browser-tabs">
                            <div class="mm-browser-tab">
                                <img src="<?php echo esc_url(get_site_icon_url(32)); ?>" alt="Favicon" id="mm-preview-favicon">
                                <span id="mm-preview-title"><?php echo esc_html(get_bloginfo('name')); ?> - Maintenance Mode</span>
                            </div>
                        </div>
                        <div class="mm-browser-address-bar">
                            <input type="text" value="<?php echo esc_url(home_url()); ?>" readonly>
                        </div>
                    </div>
                    <iframe id="mm-preview-iframe" src="<?php echo admin_url('admin-ajax.php?action=mm_preview'); ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>