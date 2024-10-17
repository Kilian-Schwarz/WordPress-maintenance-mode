<?php
/**
 * Maintenance Mode Preview Template
 */

$text = $options['mm_text'];
$background_image_id = $options['mm_background_image_id'];
$background_image = $background_image_id ? wp_get_attachment_url($background_image_id) : '';
$background_color = $options['mm_background_color'];
$font_color = $options['mm_font_color'];
$font_size = $options['mm_font_size'];
$font_bold = $options['mm_font_bold'] ? 'bold' : 'normal';
$font_italic = $options['mm_font_italic'] ? 'italic' : 'normal';
$font_underline = $options['mm_font_underline'] ? 'underline' : 'none';
$font_strikethrough = $options['mm_font_strikethrough'] ? 'line-through' : 'none';
$enable_glitch = $options['mm_enable_glitch'];
$enable_timer = $options['mm_enable_timer'];
$timer_end_date = $options['mm_timer_end_date'];
$custom_html = $options['mm_custom_html'];
$custom_css = $options['mm_custom_css'];
$logo_image_id = $options['mm_logo_image_id'];
$logo_image = $logo_image_id ? wp_get_attachment_url($logo_image_id) : '';
$favicon_image_id = $options['mm_favicon_image_id'];
$favicon_image = $favicon_image_id ? wp_get_attachment_url($favicon_image_id) : '';
$social_links = isset($_POST['mm_social_links']) ? $_POST['mm_social_links'] : get_option('mm_social_links', array());
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo esc_html($text); ?> - Maintenance Mode</title>
    <?php if ($favicon_image): ?>
        <link rel="icon" href="<?php echo esc_url($favicon_image); ?>" sizes="32x32" />
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Include necessary CSS directly */
        <?php include plugin_dir_path(__FILE__) . '../assets/css/maintenance.css'; ?>
        body {
            color: <?php echo esc_attr($font_color); ?>;
        }
        .maintenance-message {
            font-size: <?php echo intval($font_size); ?>px;
            font-weight: <?php echo esc_attr($font_bold); ?>;
            font-style: <?php echo esc_attr($font_italic); ?>;
            text-decoration: <?php echo esc_attr($font_underline . ' ' . $font_strikethrough); ?>;
        }
        <?php echo $custom_css; // Custom CSS ?>
    </style>
</head>
<body style="<?php echo $background_image ? 'background-image: url(' . esc_url($background_image) . ');' : 'background-color: ' . esc_attr($background_color) . ';'; ?>">

<div class="maintenance-container">
    <?php if ($logo_image): ?>
        <img src="<?php echo esc_url($logo_image); ?>" alt="Logo" class="maintenance-logo">
    <?php endif; ?>

    <div class="maintenance-message <?php echo $enable_glitch ? 'glitch-enabled' : ''; ?>" title="<?php echo esc_attr($text); ?>">
        <?php echo esc_html($text); ?>
    </div>

    <?php if ($enable_timer && !empty($timer_end_date)): ?>
    <div class="countdown">
        <p>Wir sind zurück in:</p>
        <div id="countdown">
            <div><span id="days">00</span><span>Tage</span></div>
            <div><span id="hours">00</span><span>Stunden</span></div>
            <div><span id="minutes">00</span><span>Minuten</span></div>
            <div><span id="seconds">00</span><span>Sekunden</span></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($custom_html): ?>
        <div class="custom-html">
            <?php echo $custom_html; // Custom HTML ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($social_links)): ?>
        <div class="social-links">
            <?php foreach ($social_links as $url): ?>
                <?php if ($url): ?>
                    <?php
                    $parsed_url = parse_url($url);
                    $domain = isset($parsed_url['host']) ? $parsed_url['host'] : '';
                    $favicon_url = 'https://www.google.com/s2/favicons?sz=64&domain=' . $domain;
                    ?>
                    <a href="<?php echo esc_url($url); ?>" target="_blank">
                        <img src="<?php echo esc_url($favicon_url); ?>" alt="favicon" />
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>