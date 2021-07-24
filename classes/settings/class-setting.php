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
		$value = false;
		if ( '_' === $name[0] ) {
			$value = true;
			$name  = ltrim( $name, '_' );
		}
		if ( ! isset( $this->children[ $name ] ) ) {
			$this->children[ $name ] = $this->root->add( $this->slug . $this->separator . $name );
		}
		$return = $this->children[ $name ];
		if ( $value ) {
			$return = $return->get();
		}

		return $return;
	}

	/**
	 * Magic method to set a child setting's value.
	 *
	 * @param string $name  The setting name being set.
	 * @param mixed  $value The value to set.
	 */
	public function __set( $name, $value ) {
		$this->{$name}->set( $value );
	}

	/**
	 * Add a child setting.
	 *
	 * @param Setting $setting The setting to add.
	 *
	 * @return Setting|\WP_Error
	 */
	public function add( $setting ) {
		$parts                   = explode( $this->separator, $setting->get_slug() );
		$slug                    = array_pop( $parts );
		$this->children[ $slug ] = $setting;

		return $setting;
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
