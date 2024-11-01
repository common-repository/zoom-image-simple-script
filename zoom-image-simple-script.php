<?php
/*
Plugin Name: Zoom image simple script
Plugin URI: http://www.gopiplus.com/work/2021/03/21/zoom-image-wordpress-plugin/
Description: Zoom image simple script
Author: Gopi Ramasamy
Version: 1.2
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/
Tags: plugin, widget, zoom, image
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: zoom-image-simple-script
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('ZISSD_DIR')) 
	define('ZISSD_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if ( ! defined( 'ZISSD_ADMIN_URL' ) )
	define( 'ZISSD_ADMIN_URL', admin_url() . 'options-general.php?page=zoom-image-simple-script' );

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ziss-register.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ziss-query.php');

function ziss_textdomain() {
	  load_plugin_textdomain( 'zoom-image-simple-script', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_shortcode( 'ziss-zoom-image', array( 'ziss_cls_shortcode', 'ziss_shortcode' ) );

add_action('wp_enqueue_scripts', array('ziss_cls_registerhook', 'ziss_frontscripts'));
add_action('plugins_loaded', 'ziss_textdomain');
add_action('widgets_init', array('ziss_cls_registerhook', 'ziss_widgetloading'));
add_action('admin_enqueue_scripts', array('ziss_cls_registerhook', 'ziss_adminscripts'));
add_action('admin_menu', array('ziss_cls_registerhook', 'ziss_addtomenu'));

register_activation_hook(ZISSD_DIR . 'zoom-image-simple-script.php', array('ziss_cls_registerhook', 'ziss_activation'));
register_deactivation_hook(ZISSD_DIR . 'zoom-image-simple-script.php', array('ziss_cls_registerhook', 'ziss_deactivation'));
?>