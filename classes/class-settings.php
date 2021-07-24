<?php
/**
 * SettingsWP Settings represents a collection of settings.
 *
 * @package   SettingsWP
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP;

use SettingsWP\Settings\Setting;
use SettingsWP\Traits\Params_Trait;
use SettingsWP\Settings\Storage\Storage;

/**
 * Class Settings
 *
 * @package SettingsWP
 */
class Settings {

	use Params_Trait;

	/**
	 * @var Setting
	 */
	protected $settings = array();

	/**
	 * Holds the storage object.
	 *
	 * @var Storage
	 */
	protected $storage;

	/**
	 * Holds the slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Setting constructor.
	 *
	 * @param string $slug   The slug/name of the settings set.
	 * @param array  $params Optional params for the setting.
	 */
	public function __construct( $slug, $params = array() ) {

		$this->slug = $slug;
		if ( isset( $params['storage'] ) ) {
			// Test if shorthand was used.
			if ( class_exists( 'SettingsWP\\Settings\\Storage\\' . $params['storage'] ) ) {
				$params['storage'] = 'SettingsWP\\Settings\\Storage\\' . $params['storage'];
			}
		} else {
			// Default.
			$params['storage'] = 'SettingsWP\\Settings\\Storage\\Options';
		}

		$this->set_params( $params );
		$this->init();
	}

	/**
	 * Flatten a setting into a string.
	 *
	 * @return array
	 */
	public function flatten() {
		return array_keys( $this->settings );
	}

	/**
	 * Magic method to get a chainable setting.
	 *
	 * @param $name
	 *
	 * @return \SettingsWP\Settings\Setting|null
	 */
	public function __get( $name ) {
		return $this->get( $name );
	}

	/**
	 * Remove a setting.
	 *
	 * @param string $slug The setting to remove.
	 */
	public function delete( $slug ) {
		$this->remove_param( '_settings' . $this->separator . $slug );
	}

	/**
	 * Init the settings.
	 */
	protected function init() {
		$storage       = $this->get_param( 'storage' );
		$this->storage = new $storage( $this->slug );
		$this->set_param( '_settings', $this->storage->get() );
	}

	/**
	 * Find a setting.
	 *
	 * @param $search
	 */
	public function find( $search ) {
		$keys  = array_keys( $this->settings );
		$found = array();
		foreach ( $keys as $key ) {
			$parts = explode( $this->separator, $key );
			$loc   = array_search( $search, $parts, true );
			if ( false == $loc ) {
				continue;
			}
			$path           = implode( $this->separator, array_slice( $parts, 0, $loc + 1 ) );
			$found[ $path ] = $this->get( $path );
		}
		// @todo: make this work.
	}

	/**
	 * Add a setting.
	 *
	 * @param string $slug    The setting slug.
	 * @param mixed  $default The default value.
	 * @param array  $params  The params.
	 *
	 * @return Setting|\WP_Error
	 */
	public function add( $slug, $default = null, $params = array() ) {

		$exists = $this->get_param( '_register' . $this->separator . $slug );
		if ( $exists ) {
			if ( isset( $exists['_type'] ) && $exists['_type'] !== 'array' ) {
				return new \WP_Error();
			}
		}
		$current_value = $this->get_param( '_settings' . $this->separator . $slug, $default );
		$setting       = array(
			'_type'    => isset( $params['type'] ) ? $params['type'] : gettype( $default ),
			'_default' => $default,
			'_params'  => $params,
		);

		settype( $setting['_default'], $setting['_type'] );
		$this->set_param( '_register' . $this->separator . $slug, $setting );
		$this->set_param( '_settings' . $this->separator . $slug, $current_value );

		$this->settings[ $slug ] = new Settings\Setting( $slug, $this );

		return $this->settings[ $slug ];
	}

	/**
	 * Get a setting.
	 *
	 * @param string $slug The slug to get.
	 *
	 * @return Setting|null
	 */
	public function get( $slug = null ) {
		$key = '_settings';
		if ( $slug ) {
			$key .= $this->separator . $slug;
		}

		return $this->get_param( $key );
	}

	/**
	 * Set a setting's value.
	 *
	 * @param string $slug  The slag of the setting to set.
	 * @param mixed  $value The value to set.
	 *
	 * @return bool
	 */
	public function set( $slug, $value ) {
		$set     = false;
		$setting = $this->get_param( '_register' . $this->separator . rtrim( $slug, $this->separator ) );
		if ( $setting ) {
			$current = $this->get_param( '_settings' . $this->separator . $slug );
			if ( $current !== $value ) {
				$this->set_param( '_settings' . $this->separator . $slug, $value );
				$set = true;
			}
		}

		return $set;
	}

	/**
	 * Save the settings values to the storage.
	 */
	public function save() {
		$data = $this->get_param( '_settings' );
		$this->storage->set( $data );
		$this->storage->save();
	}

}
