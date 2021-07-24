<?php
/**
 * SettingsWP represents a single setting node point.
 *
 * @package   SettingsWP\Settings
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2021 David Cramer <david@digilab.co.za>
 */

namespace SettingsWP\Settings;

use SettingsWP\Traits\Params_Trait;
use SettingsWP\Settings;

/**
 * Class Setting
 *
 * @package SettingsWP\Settings
 */
class Setting {

	use Params_Trait;

	/**
	 * Holds the setting value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Holds the storage parent.
	 *
	 * @var Settings
	 */
	protected $root;

	/**
	 * Holds the slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Holds the list of children.
	 *
	 * @var Setting[]
	 */
	protected $children = array();

	/**
	 * Holds the parent.
	 *
	 * @var Setting
	 */
	protected $parent;

	/**
	 * Setting constructor.
	 *
	 * @param string   $slug The setting slug.
	 * @param Settings $root The root setting.
	 */
	public function __construct( $slug, $root = null ) {
		if ( is_null( $root ) ) {
			$root = new Settings( $slug );
		}
		$this->root = $root;
		$this->slug = $slug;
	}

	/**
	 * Set the parent setting.
	 *
	 * @param Setting $parent The parent setting.
	 */
	public function set_parent( $parent ) {
		$this->parent = $parent;
	}

	/**
	 * Magic method to chain directly to the child settings by slug.
	 *
	 * @param string $name The name/slug of the child setting.
	 *
	 * @return Setting|null
	 */
	public function __get( $name ) {
		if ( ! isset( $this->children[ $name ] ) ) {
			return null;
		}

		return $this->children[ $name ];
	}

	/**
	 * Add a child setting.
	 *
	 * @param string $slug   The setting name or slug.
	 * @param array  $params The setting params.
	 *
	 * @return Setting|\WP_Error
	 */
	public function add( $slug, $params = array() ) {
		$child                   = $this->root->add( $this->slug . $this->separator . $slug, $params );
		$this->children[ $slug ] = $child;

		return $child;
	}

	/**
	 * Get the settings slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Flatten the setting.
	 *
	 * @return array
	 */
	public function flatten() {
		return array(
			$this->get_slug() => $this->get(),
		);
	}

	/**
	 * Get the value of the setting.
	 *
	 * @return mixed
	 */
	public function get() {
		return $this->root->get( $this->slug );
	}

	/**
	 * Set the value of the setting.
	 *
	 * @param mixed $value The value to set.
	 */
	public function set( $value ) {
		$this->root->set( $this->slug, $value );
	}

	/**
	 * Save the setting value.
	 */
	public function save() {
		$this->root->save();
	}
}
