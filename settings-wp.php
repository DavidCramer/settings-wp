<?php
/*
 * Plugin Name: Settings WP
 * Plugin URI: https://cramer.co.za
 * Description: Settings manager for WordPress
 * Version: 0.0.1
 * Author: David Cramer
 * Author URI: https://cramer.co.za
 * Text Domain: settings-wp
 * License: GPL2+
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	if ( is_admin() ) {
		add_action( 'admin_notices', 'settings_wp_php_ver' );
	}
} else {
	// Includes settings and starts instance.
	include_once 'bootstrap.php';
}

function settings_wp_php_ver() {
	$message = __( 'Settings WP requires PHP version 5.6 or later. We strongly recommend PHP 5.6 or later for security and performance reasons.', 'settings-wp' );
	echo sprintf( '<div id="settings_wp_error" class="error notice notice-error"><p>%s</p></div>', esc_html( $message ) );
}
