<?php

namespace IconPressLite\Database;

use IconPressLite\Helpers\Importer;
use IconPressLite\Helpers\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base
 *
 * Utility class to handle all database operations from within our plugin
 * @package IconPressLite\Database
 */
class Base
{
	/**
	 * Holds the partial name of the table storing the collections
	 * @see Base::getTableName()
	 * @var string
	 */
	const TABLE_COLLECTIONS = '_collections';
	/**
	 * Holds the partial name of the table storing the icons
	 * @see Base::getTableName()
	 * @var string
	 */
	const TABLE_ICONS = '_icons';
	/**
	 * Holds the version of our tables
	 * @var float
	 */
	const DB_VERSION = 1.0;

	/**
	 * Retrieve the full table name
	 * @param string $table
	 * @return string
	 */
	public static function getTableName( $table )
	{
		$table = strtolower( $table );
		global $wpdb;
		if ( self::TABLE_COLLECTIONS == $table ) {
			$table = $wpdb->prefix . \IconPressLite\Base::PLUGIN_SLUG . self::TABLE_COLLECTIONS;
		}
		else if ( self::TABLE_ICONS == $table ) {
			$table = $wpdb->prefix . \IconPressLite\Base::PLUGIN_SLUG . self::TABLE_ICONS;
		}
		return $table;
	}

	/**
	 * Check to see whether or not our table have been installed.
	 *
	 * [::1] Install if not
	 * [::2] Upgrade if necessary
	 *
	 * @see register_activation_hook()
	 * @see add_action('init')
	 */
	public static function checkTables()
	{
		$optName = Option::getOptionName( Option::DB_VERSION_OPT_NAME );
		$optVersion = get_option( $optName );

		//#! Install
		if ( empty( $optVersion ) ) {
			self::__createTables();
			update_option( $optName, self::DB_VERSION );
		}
		//#! Upgrade
		elseif ( version_compare( $optVersion, self::DB_VERSION, '<' ) ) {
			self::__upgradeTables();
			update_option( $optName, self::DB_VERSION );
		}
	}

	/**
	 * Cleanup
	 *
	 * [::1] Delete our tables
	 * [::2] Delete option
	 */
	public static function cleanup()
	{
		global $wpdb;
		$tblCollections = self::getTableName( self::TABLE_COLLECTIONS );
		$tblIcons = self::getTableName( self::TABLE_ICONS );
		$wpdb->query( "DROP TABLE IF EXISTS {$tblCollections}" );
		$wpdb->query( "DROP TABLE IF EXISTS {$tblIcons}" );
		delete_option( Option::getOptionName( Option::DB_VERSION_OPT_NAME ) );
		delete_option( 'iconpress_dash_auth_info' );
		delete_option( 'iconpress_iconfinder' );
		delete_transient( 'iconpress_iconfinder_user_details' );
	}

	private static function __createTables()
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		self::__createTableCollections( $wpdb );
		self::__createTableIcons( $wpdb );
	}

	private static function __upgradeTables()
	{
	}

	private static function __createTableCollections( $wpdb )
	{
		$tableName = self::getTableName( self::TABLE_COLLECTIONS );
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
  `ID` BIGINT(49) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `version` VARCHAR(25) NOT NULL DEFAULT '0.1',
  `author_name` VARCHAR(255) NOT NULL,
  `author_url` VARCHAR(255) NOT NULL,
  `license_name` VARCHAR(125) NOT NULL,
  `license_url` VARCHAR(125) NOT NULL,
  `identifier` VARCHAR(125) NOT NULL,
  PRIMARY KEY  (ID)
) $charset_collate;";

		dbDelta( $sql );
	}

	private static function __createTableIcons( $wpdb )
	{
		$tableName = self::getTableName( self::TABLE_ICONS );
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
  `ID` BIGINT(49) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `download_url` VARCHAR(255) NOT NULL,
  `collection_id` BIGINT(49) NOT NULL DEFAULT 0,
  `collection_identifier` VARCHAR(255) NOT NULL,
  `style` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (ID)
) $charset_collate;";

		dbDelta( $sql );
	}
}
