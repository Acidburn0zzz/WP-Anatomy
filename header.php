<?php if( defined('WP_DEBUG') && true !== WP_DEBUG) {
    ob_start('ob_html_compress');
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php wpa_title(); ?></title>
<meta name="MobileOptimized" content="width" />
<meta name="HandheldFriendly" content="True" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimal-ui, minimum-scale=1.0, maximum-scale=1.0" />
<?php wp_head(); ?>
<style>body{opacity:0}</style>
</head>
<?php denied_IE_2_10(); ?>
<body <?php body_class(); ?> data-hash="<?php wpa_fontbase64(true); ?>" data-a="<?php echo admin_url('admin-ajax.php'); ?>" >
<div id="wrap">
    <header>
        <div class="row">
            <a href="<?php echo site_url(); ?>" class="logo"></a>
            <a class="nav-icon" href=""><i></i><i></i><i></i></a>
            <nav>
            <?php if ( has_nav_menu( 'primary_menu' ) ) {
                wp_nav_menu(array('container' => false, 'items_wrap' => '<ul id="%1$s">%3$s</ul>', 'theme_location'  => 'primary_menu'));
            } ?>
            </nav>
            <?php get_search_form(); ?>
        </div>
    </header>
