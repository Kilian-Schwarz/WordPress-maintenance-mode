<?php
/**
 * Maintenance mode template that's shown to logged out users.
 *
 * @package   maintenance-mode
 * @copyright Copyright (c) 2024, Kilian Schwarz
 * @license   GPL2+
 */
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'assets/css/maintenance.css', dirname( __FILE__ ) ); ?>">

    <!-- Embedding @font-face within the document -->
    <style>
    @font-face {
        font-family: 'Fira Mono';
        src: url('<?php echo plugins_url( 'assets/fonts/FiraMono-Medium.woff', dirname( __FILE__ ) ); ?>');
    }
    </style>

    <!-- Adding favicon -->
    <link rel="icon" href="<?php echo plugins_url( 'assets/images/favicon.ico', dirname( __FILE__ ) ); ?>" type="image/x-icon">
</head>
<body>

<div title="Maintenance Mode">Maintenance Mode</div>

</body>
</html>