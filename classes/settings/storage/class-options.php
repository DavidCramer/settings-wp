<?php
/**
 * Storage Options. handles storing setting in WP Options.
 *
 * @package   SettingsWP\Settings\Storage
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP\Settings\Storage;

/**
 * Class Options
 *
 * @package SettingsWP\Settings\Storage
 */
class Options extends Storage {

	/**
	 * Load the data from storage source.
	 *
	 * @return mixed
	 */
	protected function load() {
		$data = get_option( $this->slug, array() );
		if ( ! empty( $data ) ) {
			$data = json_decode( $data, true );
		}

		return $data;
	}

	/**
	 * Save the data to the option.
	 *
	 * @return bool|void
	 */
	public function save() {
		$data = wp_json_encode( $this->get(), JSON_PRETTY_PRINT );

		return update_option( $this->slug, $data );
	}

}
