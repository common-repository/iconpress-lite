<?php

namespace IconPressLite\Database;

use IconPressLite__enshrined\svgSanitize\Sanitizer;
use IconPressLite\Helpers;
use IconPressLite\Helpers\Option;
use IconPressLite\Helpers\FileSystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Icons
 *
 * Utility class that provides methods to interact with the Icons table
 *
 * @package IconPressLite\Database
 */
class Icons extends Base
{
	/**
	 * Holds the cached instance of the WP File System class used
	 * @var null|\WP_Filesystem_Base
	 */
	private static $_fsCache = null;

	/**
	 * Add bulk icons
	 *
	 * @param int $collectionID
	 * @param string $identifier
	 * @param array $icons
	 * @return bool|false|int
	 */
	public static function addMultiple( $collectionID, $identifier, $icons = [] )
	{
		if ( empty( $collectionID ) || empty( $icons ) ) {
			return false;
		}
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = "INSERT INTO {$t} (`collection_id`, `collection_identifier`, `name`, `title`, `style`) VALUES ";
		$updated = 0;
		$runQuery = false;
		foreach ( $icons as $icon ) {
			$iconID = self::getID( $collectionID, $icon['name'] );
			//#! Add
			if ( $iconID < 1 ) {
				$runQuery = true;
				$query .= sprintf( "('%d', '%s', '%s', '%s', '%s'),", $collectionID, $identifier, $icon['name'], $icon['title'], $icon['style'] );
			}
			//#! Update
			else {
				$r = self::update( $iconID, $icon );
				if ( false !== $r ) {
					$updated++;
				}
			}
		}
		if ( $runQuery ) {
			return $wpdb->query( rtrim( $query, ',' ) . ';' );
		}
		return $updated;
	}

	/**
	 * Add a single icon
	 *
	 * @param int $collectionID
	 * @param string $iconName
	 * @param string $iconTitle
	 * @param string $icon
	 * @param string $iconStyle
	 * @return bool|false|int
	 */
	public static function add( $collectionID, $iconName, $iconTitle, $icon, $iconStyle = '' )
	{
		if ( empty( $collectionID ) || empty( $iconName ) || empty( $iconTitle ) || empty( $icon ) ) {
			return false;
		}
		$iconID = self::getID( $collectionID, $iconName );
		if ( ! empty( $iconID ) ) {
			$columns = [
				'name' => $iconName,
				'title' => $iconTitle,
				'style' => $iconStyle,
			];
			return self::update( $iconID, $columns );
		}

		global $wpdb;
		return $wpdb->insert(
			Base::getTableName( Base::TABLE_ICONS ),
			[
				'collection_id' => $collectionID,
				'name' => $iconName,
				'title' => $iconTitle,
				'style' => $iconStyle,
			],
			[ '%d', '%s', '%s', '%s', '%s' ]
		);
	}

	public static function getID( $collectionID, $name )
	{
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT `ID` FROM {$t} WHERE `collection_id`=%d AND `name`='%s'", $collectionID, $name );
		return (int)$wpdb->get_var( $query );
	}

	public static function update( $id, $columns = [] )
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
			self::getTableName( self::TABLE_ICONS ),
			$columns,
			$where = [ 'ID' => $id ],
			$dataFormat,
			$whereFormat = [ '%d' ]
		);
	}

	public static function get( $collectionID, $offset = 0, $count = 100 )
	{
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT * FROM {$t} WHERE `collection_id`=%d LIMIT %d,%d", $collectionID, $offset, $count );
		return $wpdb->get_results( $query );
	}

	public static function getIconDetails( $iconID, $what = array() )
	{
		global $wpdb;

		$i = Base::getTableName( Base::TABLE_ICONS );
		$c = Base::getTableName( Base::TABLE_COLLECTIONS );

		$s = '*';

		if( !empty($what) ){
			$w = [];
			foreach ($what as $value) {
				$w[] = "`{$c}`." . $value;
			}
			$s = implode(',', $w);
		}

		$query = sprintf("
			SELECT {$s}
			FROM `{$c}`
			INNER JOIN `{$i}` ON `{$c}`.ID = `{$i}`.collection_id
			AND `{$i}`.ID = %d
			", $iconID );
		return $wpdb->get_row( $query );
	}

	/**
	 * This method will retrieve all the icons limited to a count.
	 * `after` is the last icon's ID. If `after` is provided, it'll show the next batch of icons.
	 */
	public static function getAfter( $collectionID, $after = 0, $count = 100 )
	{
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT * FROM {$t} WHERE `ID` > '%d' AND `collection_id`=%d ORDER BY `ID` LIMIT %d", $after, $collectionID, $count );
		return $wpdb->get_results( $query );
	}

	public static function delete( $id )
	{
		if ( empty( $id ) ) {
			return false;
		}
		global $wpdb;
		return $wpdb->delete( self::getTableName( self::TABLE_ICONS ), [ 'ID' => $id ], [ '%d' ] );
	}


	public static function search( $search, $offset = 0, $count = 100 )
	{
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT * FROM {$t} WHERE `title` LIKE '%%%s%%' LIMIT %d,%d", $search, $offset, $count );
		return $wpdb->get_results( $query );
	}

	public static function count( $collectionID )
	{
		if ( empty( $collectionID ) ) {
			return 0;
		}
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT COUNT(`ID`) FROM {$t} WHERE `collection_id`=%d", $collectionID );
		return (int)$wpdb->get_var( $query );
	}

	public static function searchCount( $search )
	{
		if ( empty( $search ) ) {
			return 0;
		}
		global $wpdb;
		$t = Base::getTableName( Base::TABLE_ICONS );
		$query = sprintf( "SELECT COUNT(`ID`) FROM {$t} WHERE `title` LIKE '%%%s%%'", $search );
		return (int)$wpdb->get_var( $query );
	}

	public static function getAll( $maxResults = 100 )
	{
		global $wpdb;
		$t = self::getTableName( self::TABLE_ICONS );
		$query = sprintf( "SELECT * FROM {$t} LIMIT 0,%d", $maxResults );
		return $wpdb->get_results( $query );
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array
	 */
	public static function restAPI_getAllIcons( $wpr )
	{
		$count = $wpr->get_param( 'count' ) ? intval( $wpr->get_param( 'count' ) ) : 10;
		$after = $wpr->get_param( 'after' ) ? intval( $wpr->get_param( 'after' ) ) : 0;

		$collection_identifier = wp_strip_all_tags( $wpr->get_param( 'collection_identifier' ) );

		// @todo: return to ID once IF fixes their endpoint
		$collection_id = Collections::getID( $collection_identifier );

		if ( ! empty( $collection_id ) ) {
			$iObj = self::getAfter( $collection_id, $after, $count );
		}
		else {
			$iObj = self::getAll();
		}

		$output = [];
		$output['total_count'] = self::count( $collection_id );

		foreach ( $iObj as $key => $value ) {

			$output['icons'][$key] = [
				'icon_id' => $value->ID,
				'iconset_id' => $value->collection_id,
				'identifier' => $value->collection_identifier,
				'name' => $value->name,
				'title' => $value->title,
				'style' => $value->style,
				'type' => 'local',
				'internal_id' => 'local_' . $value->ID,
				'preview_url' => 'svg/' . $value->collection_identifier . '/symbol/svg/sprite.symbol.svg#' . $value->name,
				'download_url' => 'svg/' . $value->collection_identifier . '/symbol/svg/sprite.symbol.svg',
				'is_premium' => false,
				'is_last' => false,
			];

			if ( $key == ( $count - 1 ) ) {
				$output['icons'][$key]['is_last'] = true;
			}
		}
		return $output;
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array|string
	 */
	public static function restAPI_searchIcons( $wpr )
	{
		$count = $wpr->get_param( 'count' ) ? intval( $wpr->get_param( 'count' ) ) : 10;
		$offset = $wpr->get_param( 'offset' ) ? intval( $wpr->get_param( 'offset' ) ) : 0;

		$searchKeyword = wp_strip_all_tags( $wpr->get_param( 'q' ) );

		if ( ! empty( $searchKeyword ) ) {
			$iObj = self::search( $searchKeyword, $offset, $count );

			$output = [
				'total_count' => self::searchCount( $searchKeyword ),
				'icons' => []
			];

			foreach ( $iObj as $key => $value ) {
				$output['icons'][$key] = [
					'icon_id' => $value->ID,
					'iconset_id' => $value->collection_id,
					'identifier' => $value->collection_identifier,
					'name' => $value->name,
					'title' => $value->title,
					'style' => $value->style,
					'type' => 'local',
					'internal_id' => 'local_' . $value->ID,
					'preview_url' => 'svg/' . $value->collection_identifier . '/symbol/svg/sprite.symbol.svg#' . $value->name,
					'download_url' => 'svg/' . $value->collection_identifier . '/symbol/svg/sprite.symbol.svg',
					'is_premium' => false,
				];
			}
			return $output;
		}
		return 'invalid';
	}

	protected static function deleteIcon( $icon = '' ){

		if( empty($icon) ) return false;

		$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), [] );

		if ( !isset( $saved_collections['default'] ) ) {
			wp_send_json_error( __( 'No previously saved icons.', 'iconpress' ) );
		}

		$saved_collections['default'] = array_filter($saved_collections['default'], function($e) use ($icon) {
		    return $e['internal_id'] != $icon;
		});

		// rearrange array
		$saved_collections['default'] = array_values( $saved_collections['default'] );

		// update option
		update_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), $saved_collections );

		// delete individual SVG
		if ( ! FileSystem::deleteSvg( $icon ) && !empty($saved_collections['default'])) {
			return false;
		}

		return true;
	}

	public static function restAPI_DeleteIcon( $wpr )
	{
		$errors = [];

		if ( $wpr instanceof \WP_REST_Request ) {

			$internal_id = wp_strip_all_tags( $wpr->get_param( 'internal_id' ) );

			if( self::deleteIcon( $internal_id ) ){
				wp_send_json_success( __( 'Icon removed.', 'iconpress' ) );
			}
			else {
				wp_send_json_error( __( 'There was an error deleting the SVG file.', 'iconpress' ) );
			}

			// Re-Generate the svg sprite
			if ( ! FileSystem::generateSvgSprite()) {
				wp_send_json_error( __( 'Icon was removed but there was an error regenerating the sprite.', 'iconpress' ) );
			}

		}
		wp_send_json_error( __( 'Sorry, invalid request.', 'iconpress' ) );
	}


	public static function restAPI_DeleteIcons( $wpr )
	{

		if ( $wpr instanceof \WP_REST_Request ) {

			$icons = $wpr->get_param( 'icons' );
			$error_icons = [];

			foreach ($icons as $key => $icon) {
				if( !self::deleteIcon( $icon ) ){
					$error_icons[] = $icon;
				}
			}

			if( !empty($error_icons) ){
				wp_send_json_error( sprintf( __( 'Icons "%s" couldn\'t be removed.', 'iconpress' ), implode( ', ', $error_icons ) ) );
			}

			// Re-Generate the svg sprite
			if ( ! FileSystem::generateSvgSprite()) {
				wp_send_json_error( __( 'There was an error regenerating the sprite.', 'iconpress' ) );
			}

			wp_send_json_success( __( 'Icons removed.', 'iconpress' ) );

		}
		wp_send_json_error( __( 'Sorry, invalid request.', 'iconpress' ) );
	}






	public static function restAPI_getIconInfo( $wpr ){

		if ( $wpr instanceof \WP_REST_Request ) {

			$id = (int) $wpr->get_param( 'id' );
			$type = wp_strip_all_tags( $wpr->get_param( 'type' ) );

			if( $type && $type == 'author_license' ) {
				$what = array(
					'author_name',
					'author_url',
					'license_name',
					'license_url',
				);
				wp_send_json_success( self::getIconDetails($id, $what) );
			}
			else {
				wp_send_json_success( self::getIconDetails($id) );
			}
		}

		wp_send_json_error( __( 'Sorry, invalid request.', 'iconpress' ) );
	}

	public static function getAuthorAndLicense($icon = array()) {
		if ( empty($icon) ) return;

		// apply filter to identify the endpoint
		$icon_endpoint = apply_filters( 'iconpress/icon_endpoint', array(
				'local' => rest_url( \IconPressLite\Helpers\RestAPI::ICONPRESS_NAMESPACE ) . 'get_icon_info'
			)
		);

		if( isset( $icon_endpoint[ $icon['type'] ] ) ) {

			$request = wp_remote_get( $icon_endpoint[ $icon['type'] ] . '?id=' . $icon['icon_id'] . '&type=author_license' );

			if ( is_wp_error( $request ) ) {
				wp_send_json_error( $request->get_error_messages() );
			}

			$data = wp_remote_retrieve_body( $request );
			if ( empty( $data ) ) {
				wp_send_json_error( __( 'Empty data.', 'iconpress' ) );
			}

			if ( isset( $request['error'] ) ) {
				wp_send_json_error( $request['error']['message'] . ': ' . $request['error']['description'] );
			}

			$body = json_decode( $request['body'], true );

			if(isset($body['data'])) {
				return $body['data'];
			}
		}

		return false;
	}




}
