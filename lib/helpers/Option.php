<?php

namespace IconPressLite\Helpers;

use IconPressLite\Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Option
 *
 * Helper class to store all options used throughout our plugin
 * @package IconPressLite\Helpers
 */
class Option
{

	/**
	 * Holds the name of the option storing our tables' version
	 * @var string
	 */
	const DB_VERSION_OPT_NAME = '_db_version';
	/**
	 * Holds the name of the option storing the plugin's options
	 * @var string
	 */
	const PLUGIN_OPTIONS = '_plugin_options';

	/**
	 * Holds the name of the option storing the saved user's collections
	 */
	const SAVED_COLLECTIONS = '_user_collections';


	/**
	 * Retrieve the full name of the option
	 * @param string $partialOptionName
	 * @return string
	 */
	public static function getOptionName( $partialOptionName )
	{
		return Base::PLUGIN_SLUG . $partialOptionName;
	}

	/**
	 * Retrieve all defined constants
	 * @return array
	 */
	private static function __getConstants()
	{
		$constants = [];
		try {
			$reflectionClass = new \ReflectionClass( get_class() );
			$constants = $reflectionClass->getConstants();
		}
		catch ( \ReflectionException $e ) {
		}
		return $constants;
	}

	/**
	 * Delete all options registered through this class
	 */
	public static function deleteAll()
	{
		$constants = self::__getConstants();
		if ( ! empty( $constants ) ) {
			foreach ( $constants as $k => $v ) {
				delete_option( self::getOptionName( $v ) );
			}
		}
	}
}
