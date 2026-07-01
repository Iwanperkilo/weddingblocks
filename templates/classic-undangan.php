<?php
/**
 * Classic Template Fallback for Undangan Post Type.
 * Bypasses theme headers/footers to provide a full-screen canvas.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'weddingblocks-clean-canvas' ); ?>>
    <div id="weddingblocks-invitation-container" class="weddingblocks-canvas-wrapper">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
