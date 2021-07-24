<?php
/**
 * Storage abstraction. Handles how the settings are stored and retrieved.
 *
 * @package   SettingsWP\Settings\Storage
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP\Settings\Storage;

/**
 * Class Storage
 *
 * @package SettingsWP\Settings\Storage
 */
abstract class Storage {

	/**
	 * Holds the storage slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Holds the current data.
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * Storage constructor.
	 *
	 * @param $slug
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
		$this->load();
	}

	/**
	 * Get the data.
	 *
	 * @param false $reload
	 *
	 * @return mixed
	 */
	public function get( $reload = false ) {
		if ( null === $this->data || true === $reload ) {
			$this->set( $this->load() );
		}

		return $this->data;
	}

	/**
	 * Set the data.
	 *
	 * @param $data
	 */
	public function set( $data ) {
		$this->data = $data;
	}

	/**
	 * Load the data from storage source.
	 *
	 * @return mixed
	 */
	abstract protected function load();

	/**
	 * Save the data to storage source.
	 *
	 * @return bool
	 */
	abstract public function save();

}
