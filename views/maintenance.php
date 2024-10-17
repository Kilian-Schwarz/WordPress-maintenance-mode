<?php
/**
 * Maintenance Mode Template
 */

// Retrieve options from the database
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
$custom_js = get_option('mm_custom_js', '');
$logo_image_id = get_option('mm_logo_image_id');
$logo_image = $logo_image_id ? wp_get_attachment_url($logo_image_id) : '';
$favicon_image_id = get_option('mm_favicon_image_id');
$favicon_image = $favicon_image_id ? wp_get_attachment_url($favicon_image_id) : '';
$social_links = get_option('mm_social_links', array());
$background_video_url = get_option('mm_background_video_url', '');
$seo_meta_description = get_option('mm_seo_meta_description', '');
$seo_meta_keywords = get_option('mm_seo_meta_keywords', '');
$seo_robots = get_option('mm_seo_robots', '');
$google_analytics_id = get_option('mm_google_analytics_id', '');
$visitor_count = get_option('mm_visitor_count', 0);

// Increment visitor count
update_option('mm_visitor_count', $visitor_count + 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo esc_html(get_bloginfo('name')); ?> - Maintenance Mode</title>
    <?php if ($favicon_image): ?>
        <link rel="icon" href="<?php echo esc_url($favicon_image); ?>" sizes="32x32" />
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($seo_meta_description): ?>
        <meta name="description" content="<?php echo esc_attr($seo_meta_description); ?>">
    <?php endif; ?>
    <?php if ($seo_meta_keywords): ?>
        <meta name="keywords" content="<?php echo esc_attr($seo_meta_keywords); ?>">
    <?php endif; ?>
    <?php if ($seo_robots): ?>
        <meta name="robots" content="<?php echo esc_attr($seo_robots); ?>">
    <?php endif; ?>
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

<?php if ($background_video_url): ?>
    <div class="video-background">
        <video autoplay muted loop>
            <source src="<?php echo esc_url($background_video_url); ?>" type="video/mp4">
        </video>
    </div>
<?php endif; ?>

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

    <div class="visitor-count">
        Insgesamt <?php echo intval($visitor_count + 1); ?> Besucher haben diese Seite aufgerufen.
    </div>
</div>

<?php if ($enable_timer && !empty($timer_end_date)): ?>
<script>
    // Countdown Script
    (function(){
        var endDate = new Date("<?php echo esc_js(date('M d, Y H:i:s', strtotime($timer_end_date))); ?>");
        var countdown = document.getElementById("countdown");
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

<?php if ($google_analytics_id): ?>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($google_analytics_id); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($google_analytics_id); ?>');
</script>
<?php endif; ?>

<?php if ($custom_js): ?>
<script>
    <?php echo $custom_js; ?>
</script>
<?php endif; ?>

</body>
</html>