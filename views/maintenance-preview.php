<?php
/**
 * Maintenance Mode Preview Template
 */

// Use options from the $options array instead of get_option()

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
$custom_html = $options['mm_custom_html'];
$custom_css = $options['mm_custom_css'];
$logo_image_id = $options['mm_logo_image_id'];
$logo_image = $logo_image_id ? wp_get_attachment_url($logo_image_id) : '';
$favicon_image_id = $options['mm_favicon_image_id'];
$favicon_image = $favicon_image_id ? wp_get_attachment_url($favicon_image_id) : '';
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

    <?php if ($custom_html): ?>
        <div class="custom-html">
            <?php echo $custom_html; // Custom HTML ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>