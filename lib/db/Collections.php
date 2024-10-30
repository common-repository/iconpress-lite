<?php

namespace IconPressLite\Database;

use IconPressLite\Helpers;
use IconPressLite\Helpers\Option as Option;
use IconPressLite\Helpers\FileSystem as FileSystem;
use IconPressLite\Helpers\RestAPI as RestAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class _Collections
 *
 * Utility class that provides methods to interact with the Collections table
 *
 * @package IconPressLite\Database
 */
class Collections extends Base
{
	public static function add( $identifier, $name, $version = '0.1', $author_name, $author_url, $license_name, $license_url )
	{
		if ( self::getID( $identifier ) > 0 ) {
			return self::update( $identifier, [
				'name' => $name,
				'version' => $version,
				'author_name' => $author_name,
				'author_url' => $author_url,
				'license_name' => $license_name,
				'license_url' => $license_url,
			] );
		}

		global $wpdb;
		return $wpdb->insert( self::getTableName( self::TABLE_COLLECTIONS ),
			[
				'identifier' => $identifier,
				'name' => $name,
				'version' => $version,
				'author_name' => $author_name,
				'author_url' => $author_url,
				'license_name' => $license_name,
				'license_url' => $license_url,
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );
	}

	public static function update( $identifier, $columns = array() )
	{
		if ( empty( $identifier ) || empty( $columns ) ) {
			return false;
		}

		global $wpdb;
		$dataFormat = [];
		foreach ( $columns as $key => &$value ) {
			array_push( $dataFormat, '%s' );
		}
		return $wpdb->update(
			self::getTableName( self::TABLE_COLLECTIONS ),
			$columns,
			$where = [ 'identifier' => $identifier ],
			$dataFormat,
			$whereFormat = [ '%s' ]
		);
	}

	public static function getID( $identifier )
	{
		global $wpdb;
		$t = self::getTableName( self::TABLE_COLLECTIONS );
		$query = sprintf( "SELECT `ID` FROM {$t} WHERE `identifier` = '%s'", $identifier );
		return $wpdb->get_var( $query );
	}

	public static function get( $id = '*' )
	{
		global $wpdb;
		$t = self::getTableName( self::TABLE_COLLECTIONS );
		if ( '*' == $id ) {
			$collections = array();
			$data = $wpdb->get_results( "SELECT `ID` FROM {$t}", ARRAY_N );
			if ( ! empty( $data ) ) {
				foreach ( $data as $entry ) {
					array_push( $collections, $entry[0] );
				}
			}
			return $collections;
		}
		$query = sprintf( "SELECT * FROM {$t} WHERE `ID`='%s'", $id );
		return $wpdb->get_row( $query );
	}

	public static function getAll( $after = 0, $maxResults = 3 )
	{
		global $wpdb;
		$t = self::getTableName( self::TABLE_COLLECTIONS );
		$query = sprintf( "SELECT * FROM {$t} WHERE `ID` > '%d' ORDER BY `ID` LIMIT %d", $after, $maxResults );
		return $wpdb->get_results( $query );
	}

	public static function count()
	{
		global $wpdb;
		$t = self::getTableName( self::TABLE_COLLECTIONS );
		$query = "SELECT COUNT(`ID`) FROM {$t}";
		return (int)$wpdb->get_var( $query );
	}

	public static function delete( $id )
	{
		if ( empty( $id ) ) {
			return false;
		}
		global $wpdb;
		return $wpdb->delete( self::getTableName( self::TABLE_COLLECTIONS ), [ 'ID' => $id ], [ '%d' ] );
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array
	 */
	public static function restAPI_getAllCollections( $wpr )
	{
		$count = $wpr->get_param( 'count' ) ? intval( $wpr->get_param( 'count' ) ) : 10;
		$after = $wpr->get_param( 'after' ) ? intval( $wpr->get_param( 'after' ) ) : 0;

		$collection = $wpr->get_param( 'collection' );

		// in case a specific collection_id is called
		if ( ! empty( $collection ) ) {
			$collections = self::get( $collection );
		}
		// Get all
		else {
			$collections = self::getAll( $after, $count );
		}

		$output = [];
		$output['total_count'] = self::count();

		if ( ! empty( $collections ) ) {
			foreach ( $collections as $key => $row ) {

				$output['iconsets'][$key] = [
					'iconset_id' => $row->ID,
					'name' => $row->name,
					'identifier' => $row->identifier,
					'version' => $row->version,
					'author_name' => $row->author_name,
					'author_url' => $row->author_url,
					'license_name' => $row->license_name,
					'license_url' => $row->license_url,
					'icons_count' => Icons::count( $row->ID ),
					'type' => 'local',
					'is_last' => false,
				];

				if ( $key == ( $count - 1 ) ) {
					$output['iconsets'][$key]['is_last'] = true;
				}
			}
		}
		return $output;
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array
	 */
	public static function restAPI_getUserCollections( $wpr )
	{
		return get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), [] );
	}


	/**
	 * @param \WP_REST_Request $wpr
	 */
	public static function restAPI_ajaxSaveUserCollection( $wpr )
	{
		if ( ! RestAPI::isUserAllowed() ) {
			wp_send_json_error( __( 'Sorry, you need to be an administrator to perform this action', 'iconpress' ) );
		}

		if ( $wpr instanceof \WP_REST_Request ) {
			$args = $wpr->get_json_params();

			// @todo: Commented until we figure out with the multiple collections.
			// if ( ! isset( $args['name'] ) || empty( $args['name'] ) ) {
			// 	wp_send_json_error( __( 'Sorry, the collection name is missing.', 'iconpress' ) );
			// }
			// $collectionName = sanitize_title( $args['name'] );

			if ( ! isset( $args['icons'] ) || empty( $args['icons'] ) ) {
				wp_send_json_error( __( 'Please select at least one icon.', 'iconpress' ) );
			}

			$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), [] );

			// get custom saved icons (saved through editor)
			if ( isset( $saved_collections['default'] ) ) {
				$custom_icons = array_filter( $saved_collections['default'], function ( $v, $k ) {
					if( isset($v['is_custom']) && $v['is_custom'] ) {
						return $v;
					}
				}, ARRAY_FILTER_USE_BOTH );
			}

			$current_user = wp_get_current_user();

			$collections['default'] = [];

			foreach ($args['icons'] as $icon) {

				if( !isset($icon['is_custom']) ) {

					// validate user id
					if( ! isset($icon['user_id']) || ( isset($icon['user_id']) && $icon['user_id'] != $current_user->ID ) ) {
						$icon['user_id'] = $current_user->ID;
					}
					// add local url where svg is stored
					if( !isset($icon['local_url']) ) {
						// download icon
						if( FileSystem::downloadSvg( $icon ) ) {
							$icon['local_url'] = FileSystem::getSingleSvgPath( $icon['internal_id'], false );
						}
					}
					// add missing author and license info
					if( !isset($icon['author']) || !isset($icon['license'])) {
						if( $details = Icons::getAuthorAndLicense( $icon ) ) {
							$icon['author']['name'] = $details['author_name'];
							$icon['author']['url'] = $details['author_url'];
							$icon['license']['name'] = $details['license_name'];
							$icon['license']['url'] = $details['license_url'];
						}
					}
					// this is to reassure that the item was downloaded
					if( isset($icon['local_url']) ) {
						array_push($collections['default'], $icon);
					}
				}
			}

			// add custom icons back
			if( !empty($custom_icons) ) {
				foreach ($custom_icons as $c_icon) {
					array_push($collections['default'], $c_icon);
				}
			}

			// save icons
			update_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), $collections );

			// Generate the svg sprite
			if ( ! FileSystem::generateSvgSprite() ) {
				wp_send_json_error( __( 'There was an error creating/updating the SVG SPRITE.', 'iconpress' ) );
			}
			// remove unused svg files
			FileSystem::cleanupUnusedSvg();

			wp_send_json_success( $collections['default'] );
		}
		wp_send_json_error( __( 'Sorry, invalid request.', 'iconpress' ) );
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array|\WP_Error
	 */
	public static function restAPI_getCollection( $wpr )
	{
		if ( $wpr instanceof \WP_REST_Request ) {
			$collection_id = wp_strip_all_tags( $wpr->get_param( 'collection_id' ) );
			$collection_identifier = wp_strip_all_tags( $wpr->get_param( 'collection_identifier' ) );
			// In case identifier is provided
			if ( $collection_identifier ) {
				return self::get( self::getID( $collection_identifier ) );
			}
			return self::get( $collection_id );
		}
		return new \WP_Error( __( 'Sorry, nothing found', 'iconpress' ) );
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array|\WP_Error
	 */
	public static function restAPI_getSvgSpriteContent( $wpr )
	{
		if ( $wpr instanceof \WP_REST_Request ) {
			if( $svg_content = FileSystem::getContent( 'default.svg' ) ) {
				wp_send_json_success( $svg_content );
			}
			else {
				wp_send_json_error( __( "Couldn't get the sprite.", 'iconpress') );
			}
		}
	}

}
