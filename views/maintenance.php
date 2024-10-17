<?php
/**
 * Maintenance Mode Template
 */

// Zugriff auf die globalen Optionen aus der Vorschau
global $mm_options;

if (isset($mm_options) && is_array($mm_options)) {
    // Verwende die Optionen aus der Vorschau
    $options = $mm_options;
} else {
    // Lade die Optionen aus der Datenbank
    $options = array(
        'mm_text' => get_option('mm_text', 'Wir sind bald zurück!'),
        'mm_background_image_id' => get_option('mm_background_image_id'),
        'mm_background_color' => get_option('mm_background_color', '#131313'),
        'mm_font_color' => get_option('mm_font_color', '#ffffff'),
        'mm_font_size' => get_option('mm_font_size', '48'),
        'mm_font_bold' => get_option('mm_font_bold') ? 'bold' : 'normal',
        'mm_font_italic' => get_option('mm_font_italic') ? 'italic' : 'normal',
        'mm_font_underline' => get_option('mm_font_underline') ? 'underline' : 'none',
        'mm_font_strikethrough' => get_option('mm_font_strikethrough') ? 'line-through' : 'none',
        'mm_enable_glitch' => get_option('mm_enable_glitch'),
        'mm_enable_timer' => get_option('mm_enable_timer'),
        'mm_timer_end_date' => get_option('mm_timer_end_date'),
        'mm_custom_html' => get_option('mm_custom_html', ''),
        'mm_custom_css' => get_option('mm_custom_css', ''),
        'mm_logo_image_id' => get_option('mm_logo_image_id'),
        'mm_favicon_image_id' => get_option('mm_favicon_image_id'),
        'mm_social_links' => get_option('mm_social_links', array()),
    );
}

// Jetzt verwenden wir $options für die Variablen
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
$social_links = $options['mm_social_links'];

// Plugin URL für die Font-Datei
$plugin_url = plugin_dir_url(dirname(__FILE__));
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
        /* Einbinden der Font-Datei mit korrektem Pfad */
        @font-face {
            font-family: 'Fira Mono';
            src: url('<?php echo esc_url($plugin_url . 'assets/css/FiraMono-Medium.woff'); ?>') format('woff');
        }

        <?php include plugin_dir_path(__FILE__) . '../assets/css/maintenance.css'; ?>

        body {
            color: <?php echo esc_attr($font_color); ?>;
        }
        .maintenance-message {
            font-size: <?php echo intval($font_size); ?>px;
            font-weight: <?php echo esc_attr($font_bold); ?>;
            font-style: <?php echo esc_attr($font_italic); ?>;
            text-decoration: <?php echo esc_attr($font_underline . ' ' . $font_strikethrough); ?>;
            user-select: none; /* Text kann nicht markiert werden */
        }
        <?php echo $custom_css; // Benutzerdefiniertes CSS ?>
    </style>
</head>
<body style="<?php echo $background_image ? 'background-image: url(' . esc_url($background_image) . ');' : 'background-color: ' . esc_attr($background_color) . ';'; ?> overflow: hidden;">

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
            <div><span id="years">00</span><span>Jahre</span></div>
            <div><span id="months">00</span><span>Monate</span></div>
            <div><span id="days">00</span><span>Tage</span></div>
            <div><span id="hours">00</span><span>Stunden</span></div>
            <div><span id="minutes">00</span><span>Minuten</span></div>
            <div><span id="seconds">00</span><span>Sekunden</span></div>
        </div>
    </div>
    <script>
    // Countdown Script
    (function(){
        var endDate = new Date("<?php echo esc_js(date('M d, Y H:i:s', strtotime($timer_end_date))); ?>");
        var countdown = document.getElementById("countdown");
        var yearsSpan = document.getElementById("years");
        var monthsSpan = document.getElementById("months");
        var daysSpan = document.getElementById("days");
        var hoursSpan = document.getElementById("hours");
        var minutesSpan = document.getElementById("minutes");
        var secondsSpan = document.getElementById("seconds");

        function updateCountdown() {
            var now = new Date();
            var distance = endDate - now;

            if (distance < 0) {
                clearInterval(interval);
                document.querySelector('.countdown p').innerText = "Wir sind wieder online!";
                countdown.style.display = 'none';
                return;
            }

            var totalSeconds = Math.floor(distance / 1000);

            var years = Math.floor(totalSeconds / (365 * 24 * 60 * 60));
            totalSeconds %= 365 * 24 * 60 * 60;

            var months = Math.floor(totalSeconds / (30 * 24 * 60 * 60));
            totalSeconds %= 30 * 24 * 60 * 60;

            var days = Math.floor(totalSeconds / (24 * 60 * 60));
            totalSeconds %= 24 * 60 * 60;

            var hours = Math.floor(totalSeconds / (60 * 60));
            totalSeconds %= 60 * 60;

            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;

            // Update display
            if (years > 0) {
                yearsSpan.textContent = ('0' + years).slice(-2);
                yearsSpan.parentElement.style.display = 'inline-block';
            } else {
                yearsSpan.parentElement.style.display = 'none';
            }

            if (months > 0 || years > 0) {
                monthsSpan.textContent = ('0' + months).slice(-2);
                monthsSpan.parentElement.style.display = 'inline-block';
            } else {
                monthsSpan.parentElement.style.display = 'none';
            }

            if (days > 0 || months > 0 || years > 0) {
                daysSpan.textContent = ('0' + days).slice(-2);
                daysSpan.parentElement.style.display = 'inline-block';
            } else {
                daysSpan.parentElement.style.display = 'none';
            }

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