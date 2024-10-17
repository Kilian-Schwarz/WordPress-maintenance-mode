<?php
/**
 * Maintenance Mode Template
 */

$text = get_option('mm_text', 'Wir sind bald zurück!');
$background_image_id = get_option('mm_background_image_id');
$background_image = $background_image_id ? wp_get_attachment_url($background_image_id) : '';
$background_color = get_option('mm_background_color', '#131313');
$font_color = get_option('mm_font_color', '#ffffff');
$font_size = get_option('mm_font_size', '48');
$font_bold = get_option('mm_font_bold') ? 'bold' : 'normal';
$font_italic = get_option('mm_font_italic') ? 'italic' : 'normal';
$font_underline = get_option('mm_font_underline') ? 'underline' : 'none';
$font_strikethrough = get_option('mm_font_strikethrough') ? 'line-through' : 'none';
$enable_glitch = get_option('mm_enable_glitch');
$enable_timer = get_option('mm_enable_timer');
$timer_end_date = get_option('mm_timer_end_date');
$custom_html = get_option('mm_custom_html', '');
$custom_css = get_option('mm_custom_css', '');
$logo_image_id = get_option('mm_logo_image_id');
$logo_image = $logo_image_id ? wp_get_attachment_url($logo_image_id) : '';
$favicon_image_id = get_option('mm_favicon_image_id');
$favicon_image = $favicon_image_id ? wp_get_attachment_url($favicon_image_id) : '';
$social_links = get_option('mm_social_links', array());
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo esc_html(get_bloginfo('name')); ?> - Maintenance Mode</title>
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
        <?php echo $custom_css; // Benutzerdefiniertes CSS ?>
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

    <?php if ($enable_timer): ?>
    <div class="countdown">
        <p>Wir sind zurück in:</p>
        <div id="countdown">
            <div><span id="days">00</span><span>Tage</span></div>
            <div><span id="hours">00</span><span>Stunden</span></div>
            <div><span id="minutes">00</span><span>Minuten</span></div>
            <div><span id="seconds">00</span><span>Sekunden</span></div>
        </div>
    </div>
    <script>
    // Countdown Script
    (function(){
        var endDate = new Date("<?php echo esc_js(date('M d, Y H:i:s', strtotime($timer_end_date))); ?>").getTime();
        var countdown = document.getElementById("countdown");
        var daysSpan = document.getElementById("days");
        var hoursSpan = document.getElementById("hours");
        var minutesSpan = document.getElementById("minutes");
        var secondsSpan = document.getElementById("seconds");

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = endDate - now;

            if (distance < 0) {
                clearInterval(interval);
                document.querySelector('.countdown p').innerText = "Wir sind wieder online!";
                countdown.style.display = 'none';
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
            var minutes = Math.floor((distance / (1000 * 60)) % 60);
            var seconds = Math.floor((distance / 1000) % 60);

            daysSpan.textContent = ('0' + days).slice(-2);
            hoursSpan.textContent = ('0' + hours).slice(-2);
            minutesSpan.textContent = ('0' + minutes).slice(-2);
            secondsSpan.textContent = ('0' + seconds).slice(-2);
        }

        updateCountdown();
        var interval = setInterval(updateCountdown, 1000);
    })();
    </script>
    <?php endif; ?>

    <?php if ($custom_html): ?>
        <div class="custom-html">
            <?php echo $custom_html; // Benutzerdefiniertes HTML ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($social_links)): ?>
        <div class="social-links">
            <?php foreach ($social_links as $platform => $url): ?>
                <?php if ($url): ?>
                    <a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo ucfirst($platform); ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>