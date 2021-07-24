<?php
/**
 * Storage File. handles storing setting in in a flat file.
 *
 * @package   SettingsWP\Settings\Storage
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP\Settings\Storage;

/**
 * Class File
 *
 * @package SettingsWP\Settings\Storage
 */
class File extends Storage {

	/**
	 * Get the file path.
	 *
	 * @return string
	 */
	protected function get_file() {
		$dir = wp_upload_dir();

		return $dir['basedir'] . '/' . $this->slug . '.json';
	}

	/**
	 * Load the data from storage source.
	 *
	 * @return mixed
	 */
	protected function load() {
		$file = $this->get_file();
		$data = array();
		if ( file_exists( $file ) ) {
			$content = file_get_contents( $file );
			$data    = json_decode( $content, true );
		}

		return $data;
	}

	/**
	 * Save the data to a file.
	 *
	 * @return bool|void
	 */
	public function save() {
		$data = wp_json_encode( $this->get(), JSON_PRETTY_PRINT );
		$file = $this->get_file();
		$fp   = fopen( $file, 'w+' );
		fputs( $fp, $data );
		fclose( $fp );

		return true;
	}

}
