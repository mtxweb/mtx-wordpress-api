<?php
/**
 * Plugin Name: Customize Image Gallery Control
 * Version: 0.1
 * Description: Adds a Customizer control for gallery.
 * Author: XWP
 * Plugin URI: https://github.com/xwp/wp-customize-image-gallery-control
 * Text Domain: customize-image-gallery-control
 *
 * @package CustomizeImageGalleryControl
 */

global $customize_image_gallery_control_plugin;

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/php/class-plugin.php';
	$class = 'CustomizeImageGalleryControl\\Plugin';
	$customize_image_gallery_control_plugin = new $class();
	
}