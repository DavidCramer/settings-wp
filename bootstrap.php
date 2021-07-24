<?php
/**
 * Settings WP Bootstrap.
 *
 * @package   SettingsWP
 * @author    David Cramer
 * @license   GPL-2.0+
 * @copyright 2021/07/24 David Cramer
 */

/**
 * Autoload for classes that are in the same SettingsWP namespace.
 *
 * @param string $class Class name.
 *
 * @return void
 */
function settings_wp_autoloader( $class ) {
	// Assume we're using namespaces (because that's how the plugin is structured).
	$namespace = explode( '\\', $class );
	$root      = array_shift( $namespace );
	if ( 'SettingsWP' !== $root ) {
		return;
	}
	// If a class ends with "Trait" then prefix the filename with 'trait-', else use 'class-'.
	$class_trait = preg_match( '/Trait$/', $class ) ? 'trait-' : 'class-';
	// Class name is the last part of the FQN.
	$class_name = array_pop( $namespace );

	// Remove "Trait" from the class name.
	if ( 'trait-' === $class_trait ) {
		$class_name = str_replace( '_Trait', '', $class_name );
	}

	// For file naming, the namespace is everything but the class name and the root namespace.
	$namespace = trim( implode( DIRECTORY_SEPARATOR, $namespace ) );

	// Get the path to our files.
	$directory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes';
	if ( ! empty( $namespace ) ) {
		$directory .= DIRECTORY_SEPARATOR . strtolower( $namespace );
	}

	// Because WordPress file naming conventions are odd.
	$file = strtolower( str_replace( '_', '-', $class_name ) );

	$file = $directory . DIRECTORY_SEPARATOR . $class_trait . $file . '.php';
	if ( file_exists( $file ) ) {
		require_once $file; // phpcs:ignore
	}
}

spl_autoload_register( 'settings_wp_autoloader', true, false );

/**
 * Helper function to create a new settings collection.
 *
 * @param string $slug   The setting collection name.
 * @param array  $params The optional config params.
 *
 * @return \SettingsWP\Settings
 */
function SettingsWP( $slug, $params = array() ) {
	return new SettingsWP\Settings( $slug, $params );
}
