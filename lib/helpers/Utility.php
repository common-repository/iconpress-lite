<?php

namespace IconPressLite\Helpers;

use IconPressLite\Helpers\Option;
use IconPressLite\Helpers\FileSystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Utility
 *
 * Various helpers
 *
 * @package IconPressLite\Helpers
 */
class Utility
{

	public static $user_id = 0;

	function __construct(){
		add_action('init', [ $this, 'getUser' ]);
	}


	public static function unslugify( $slug ) {
		$slug = str_replace( '.svg', '', $slug );
		$slug = str_replace( '-', ' ', $slug );
		$slug = str_replace( '_', ' ', $slug );
		$slug = str_replace( ',', ' ', $slug );
		$slug = str_replace( '.', ' ', $slug );
		return ucwords( $slug );
	}

	public static function getUser(){
		$current_user = wp_get_current_user();
		self::$user_id = $current_user->ID;
	}

	public static function getSettings() {
		return get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
	}

	public static function getSetting($setting, $default = '') {
		$ip_options = self::getSettings();
		return isset( $ip_options[$setting] ) ? $ip_options[$setting] : $default;
	}


}
new Utility();
