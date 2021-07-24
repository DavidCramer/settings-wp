<?php
/**
 * Params Trait handles using of setting parameters.
 *
 * @package   SettingsWP\Traits
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP\Traits;

/**
 * Trait Params_Trait
 *
 * @package SettingsWP\Traits
 */
trait Params_Trait {

	/**
	 * Holds the params.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * Holds the separator.
	 *
	 * @var string
	 */
	protected $separator = '.';

	/**
	 * Sets the params recursively.
	 *
	 * @param array $parts The parts to set.
	 * @param array $param The param being set.
	 * @param mixed $value The value to set.
	 *
	 * @return mixed
	 */
	protected function set_param_array( $parts, $param, $value ) {
		$new = $param;
		$key = array_shift( $parts );
		if ( ! empty( $parts ) ) {
			$param = isset( $param[ $key ] ) ? $param[ $key ] : array();
			$value = $this->set_param_array( $parts, $param, $value );
		}
		if ( null === $value ) {
			unset( $new[ $key ] );

			return $new;
		}
		if ( '' === $key ) {
			$new[] = $value;
		} else {
			$new[ $key ] = $value;
		}
		ksort( $new );

		return $new;

	}

	/**
	 * Set a parameter and value to the setting.
	 *
	 * @param string $param Param key to set.
	 * @param mixed  $value The value to set.
	 *
	 * @return $this
	 */
	public function set_param( $param, $value = null ) {
		$parts = explode( $this->separator, $param );
		$parts = array_map( array( $this, 'sanitize_slug' ), $parts );
		$param = array_shift( $parts );
		if ( ! empty( $parts ) ) {
			if ( ! isset( $this->params[ $param ] ) ) {
				$this->params[ $param ] = array();
			}
			$value = $this->set_param_array( $parts, $this->params[ $param ], $value );
		}

		$this->params[ $param ] = $value;

		if ( is_null( $value ) ) {
			$this->remove_param( $param );
		}

		return $this;
	}

	/**
	 * Set the whole params array.
	 *
	 * @param array $params The params to set.
	 */
	protected function set_params( array $params ) {
		foreach ( $params as $param => $value ) {
			$this->set_param( $param, $value );
		}
	}

	/**
	 * Remove a parameter.
	 *
	 * @param string $param Param key to set.
	 *
	 * @return $this
	 */
	public function remove_param( $param ) {
		$parts = explode( $this->separator, $param );
		$param = array_pop( $parts );
		if ( ! empty( $parts ) ) {
			$master = implode( $this->separator, $parts );
			$parent = $this->get_param( $master );
			unset( $parent[ $param ] );
			$this->set_param( $master, $parent );
		} else {
			unset( $this->params[ $param ] );
		}

		return $this;
	}

	/**
	 * Sanitize a slug.
	 *
	 * @param string $slug The slug to sanitize.
	 *
	 * @return string
	 */
	protected function sanitize_slug( $slug ) {
		return sanitize_file_name( $slug );
	}

	/**
	 * Get a param from a chained lookup.
	 *
	 * @param string $param_slug The slug to get.
	 *
	 * @return mixed
	 */
	protected function get_array_param( $param_slug ) {
		$parts = explode( $this->separator, ltrim( $param_slug, ':' ) );
		$parts = array_map( array( $this, 'sanitize_slug' ), $parts );
		$param = $this->params;
		while ( ! empty( $parts ) ) {
			if ( ! is_array( $param ) ) {
				$param = null; // Set to null to indicate invalid.
				break;
			}
			// Lets break here, if theres a _type and it's not an array.
			if ( isset( $param['_type'] ) && $param['_type'] !== 'array' ) {
				break;
			}
			$part    = array_shift( $parts );
			$default = null;
			if ( '' === $part ) {
				$default = array( null );
			}
			$param = isset( $param[ $part ] ) ? $param[ $part ] : $default;
		}

		return $param;
	}

	/**
	 *
	 * Check if a param exists.
	 *
	 * @param string $param_slug The param to check.
	 *
	 * @return bool
	 */
	public function has_param( $param_slug ) {
		$param = $this->get_array_param( $param_slug );

		return ! is_null( $param );
	}

	/**
	 * Get params param.
	 *
	 * @param string $param   The param to get.
	 * @param mixed  $default The default value for this param is a value is not found.
	 *
	 * @return mixed
	 */
	public function get_param( $param, $default = null ) {
		$value = $this->get_array_param( $param );

		return ! is_null( $value ) ? $value : $default;
	}

	/**
	 * Get the whole params.
	 *
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}
}
