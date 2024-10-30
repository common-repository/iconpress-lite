<?php

namespace IconPressLite\Helpers;

use IconPressLite\Helpers\FileSystem;
use IconPressLite\Helpers\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Portability
 *
 * @package IconPressLite\Helpers
 */
class Portability
{
	private static $is_exporting = false;

	/**
	 * Holds the cached instance of the WP File System class used
	 * @var null|\WP_Filesystem_Base
	 */
	private static $_fsCache = null;

	public static function restAPI_exportCollection( $wpr ){

		if( self::$is_exporting ) {
			return;
		}

		self::$is_exporting = true;

		if ( empty( self::$_fsCache ) ) {
			self::$_fsCache = FileSystem::get();
		}

		$upDir = FileSystem::$uploadsDirPath;
		$iDir = FileSystem::$uploads__IconPressDirPath;
		$cDir = FileSystem::$uploads__IconPressCollectionDirPath;

		// Get collections
		$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), array() );
		$default_collection = isset($saved_collections['default']) && !empty($saved_collections['default']) ? wp_json_encode($saved_collections['default']) : [];

		if( empty($default_collection) ) {
			wp_send_json_error( __( "Empty collection. Aborting.", 'iconpress') );
		}

		if( !class_exists('ZipArchive') ) {
			wp_send_json_error( __('ZipArchive is not enabled. Aborting!', 'iconpress') );
		}

		$zip = new \ZipArchive;
		// list files
		$filelist = self::$_fsCache->dirlist( $upDir . $iDir . $cDir);
		// archive path
		$archive_path = $upDir . $iDir . uniqid('backup_') . '.zip';
		// delete archive if it exists
		if ( self::$_fsCache->is_file( $archive_path ) ) {
			self::$_fsCache->delete( $archive_path );
		}
		// create archive
		if ( true === $zip->open( $archive_path, \ZipArchive::CREATE ) ) {
			foreach ($filelist as $key => $file) {
		        $zip->addFile( trailingslashit( $upDir . $iDir . $cDir ) . $file['name'], $file['name'] );
 			}
 			// add json
		    $zip->addFromString('iconpress.json', $default_collection);
			$zip->close();

			self::$is_exporting = false;
		} else {
			wp_send_json_error( __( 'Unable to open export file (archive) for writing.' ) );
		}

		wp_send_json_success( FileSystem::$uploadsDirUri . $iDir . basename($archive_path) );
	}

	public static function restAPI_deleteExport( $wpr ){

		if ( $wpr instanceof \WP_REST_Request ) {

			if ( empty( self::$_fsCache ) ) {
				self::$_fsCache = FileSystem::get();
			}

			$fname = FileSystem::$uploadsDirPath . FileSystem::$uploads__IconPressDirPath . $wpr->get_param( 'filename' );

			if( self::$_fsCache->is_file( $fname ) ){
				self::$_fsCache->delete( $fname );
				wp_send_json_success( __('Successfully deleted!', 'iconpress') );
			}
			wp_send_json_error( __('Unable to delete.', 'iconpress') );
		}
	}

	public static function restAPI_restore( $wpr ){

		if ( $wpr instanceof \WP_REST_Request ) {

			if( $file = $wpr->get_file_params() ) {
				if( !empty($file) && isset($file['file']) && is_array($file['file']) ){
					$file = $file['file'];
				}
				else {
					wp_send_json_error( __('File is missing.', 'iconpress') );
				}
			}
			else {
				wp_send_json_error( __('Something went wrong with the uploaded file.', 'iconpress') );
			}

			// make the import
			$import = self::importIconPressData([
				'path' => $file,
				'overwrite' => $wpr->get_param('overwrite')
			]);

			// return errors (if any)
			if( isset($import['error']) ){
				wp_send_json_error( $import['error'] );
			}

			wp_send_json_success( __('Successfully uploaded & imported data.', 'iconpress') );
		}
	}

	private static function parseJsonAndExtractFiles($file = [], $overwrite = false){

		if( empty($file) ){
			return array( 'error' => __('Empty $file.', 'iconpress') );
		}

		if ( empty( self::$_fsCache ) ) {
			self::$_fsCache = FileSystem::get();
		}

		$upDir = FileSystem::$uploadsDirPath;
		$iDir = FileSystem::$uploads__IconPressDirPath;
		$cDir = FileSystem::$uploads__IconPressCollectionDirPath;

		if( !self::$_fsCache->is_dir( $upDir . $iDir . $cDir ) ) {
			self::$_fsCache->mkdir( $upDir . $iDir . $cDir );
		}

		$uploadedFile = $file['file'];

	    $temp_folder = trailingslashit( $upDir . $iDir . 'temp' );

		//#! Extract the archive
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
		if( $result = unzip_file( $uploadedFile, $temp_folder ) ) {
			if ( is_wp_error( $result ) ) {
				return array( 'error' => $result->get_error_message() );
			}
			// delete file if not local (eg: theme)
			if( !isset($file['local']) ){
				self::$_fsCache->delete( $uploadedFile );
			}
		}

		// Get JSON file
		$json_file = $temp_folder . 'iconpress.json';

		// Get contents of json
		if( self::$_fsCache->is_file( $json_file ) ) {
			// get json's content
			$json_data = self::$_fsCache->get_contents( $json_file );
			// run a cleanup
			$json_data = self::refreshUserId($json_data);
		}
		else {
			return array( 'error' => __('iconpress.json file missing.', 'iconpress') );
		}

		return self::importDataAndCopyFiles($json_data, $temp_folder, $overwrite);
	}

	private static function importDataAndCopyFiles($json_data, $temp_folder, $overwrite, $delete_temp = true){

		if ( empty( self::$_fsCache ) ) {
			self::$_fsCache = FileSystem::get();
		}

		$upDir = FileSystem::$uploadsDirPath;
		$iDir = FileSystem::$uploads__IconPressDirPath;
		$cDir = FileSystem::$uploads__IconPressCollectionDirPath;

		FileSystem::checkUploadsDir();

		// Get collections
		$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), [] );
		$default_collection = isset($saved_collections['default']) && !empty($saved_collections['default']) ? $saved_collections['default'] : [];
		$final_collection = [];

		// overwrite duplicates
		if( $overwrite ){
			// merge default collection with the imported one, overwriting duplicates
			$final_collection['default'] = self::array_merge_recursive_distinct( $default_collection, $json_data );
			// move svg's, overwriting duplicates
			$i = 0;
			// list files
			$svg_items = self::$_fsCache->dirlist( $temp_folder );
			$len = count($svg_items);
			foreach ($svg_items as $key => $svg) {
				if( self::$_fsCache->move(
					$temp_folder . $svg['name'],
					$upDir . $iDir . $cDir . $svg['name'],
					true
				) ) {
					// after last item
					// remove temporary folder
					if( $i == $len - 1 ) {
						self::$_fsCache->delete( $temp_folder, true );
					}
				}
				$i++;
			}
		}

		// Don't overwrite,
		// instead rename duplicates
		else {

			// get duplicates
			$dupes = array_uintersect($json_data, $default_collection, function($a1, $a2){
				$diff = strcasecmp($a1['internal_id'], $a2['internal_id']);
				if ($diff != 0) return $diff;
				return 0;
			});

			// rename them
			foreach ($dupes as $key => $value) {
				if (($key = array_search($value, $json_data)) !== false) {
					$internal_id = $value['internal_id'];
					// update with new internal id
					$json_data[$key]['internal_id'] = $internal_id . uniqid('_');
					$json_data[$key]['local_url'] = str_replace( $internal_id, $json_data[$key]['internal_id'], $value['local_url']);
					$json_data[$key]['is_custom'] = true;
					$json_data[$key]['imported_internal_id'] = $internal_id;
				}
			}

			// copy files and make a bit of cleanup
			foreach ($json_data as $key => $item) {

				$internal_id = $item['internal_id'];

				// check if this one is a duplicate
				// and pass the imported internal id
				// and unset it from the array
				if( isset($item['imported_internal_id']) ) {
					$internal_id = $item['imported_internal_id'];
					unset($json_data[$key]['imported_internal_id']);
				}

				// move svgs
				if( self::$_fsCache->is_dir( $upDir . $iDir . $cDir ) && self::$_fsCache->is_file( $temp_folder . $internal_id . '.svg' ) ) {

					self::$_fsCache->copy(
						$temp_folder . $internal_id . '.svg',
						$upDir . $iDir . $cDir . $item['internal_id'] . '.svg',
						true
					);
				}
				// update local url
				$json_data[$key]['local_url'] = $iDir . $cDir . $item['internal_id'] . '.svg';
			}
			// merge json with default collection
			$final_collection['default'] = array_merge($default_collection, $json_data);
		}

		// update option
		update_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), $final_collection );

		// Generate the svg sprite
		if ( ! FileSystem::generateSvgSprite() ) {
			wp_send_json_error( __( 'There was an error creating/updating the SVG SPRITE.', 'iconpress' ) );
		}
		// remove unused svg files
		FileSystem::cleanupUnusedSvg();

		// delete temp folder
		if($delete_temp){
			self::$_fsCache->delete( $temp_folder, true );
		}

		return true;
	}

	private static function refreshUserId($json = []){

		if( empty($json) ) return;

		$json = json_decode($json, true);
		$new = [];

		foreach ($json as $key => $item) {

			$new[$key] = $item;

			$current_user = wp_get_current_user();
			if( isset($current_user->ID) ){
				$new[$key]['user_id'] = $current_user->ID;
			}
		}

		return $new;
	}

	private static function array_merge_recursive_distinct(array &$array1, array &$array2)
	{
	    $merged = $array1;
	    foreach ($array2 as $key => &$value) {
	        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
	            $merged[$key] = self::array_merge_recursive_distinct($merged[$key], $value);
	        } else {
	            $merged[$key] = $value;
	        }
	    }
	    return $merged;
	}


	private static function makeUpload( $f ) {

		if( is_array( $f ) ){

			// check if wp handle upload exists,
			// if not load necessary file
			if ( ! function_exists( '\wp_handle_upload' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$overrides = array(
			    'mimes'     => array(
				    'zip'  => 'application/zip'
				),
			    'test_form' => false
			);

			return \wp_handle_upload( $f, $overrides );
		}
		else {
			return array( 'file' => $f, 'local' => true );
		}

		return false;
	}

	/**
	 * Utility to import IconPress backups.
	 */
	public static function importIconPressData($args = []){

		$defaults = [
			'path' => '',
			'overwrite' => false
		];
		$args = wp_parse_args( $args, $defaults );

		if( empty( $args['path'] ) ){
			return array( 'error' => __('Empty filepath.', 'iconpress') );
		}

		$upload = self::makeUpload( $args['path'] );

		if ( isset( $upload['error'] ) ){
		    // Upload error occurred
			return array( 'error' => $upload['error'] );

		} else {

		    // File uploaded successfully.
			// Make the IconPress Import
			$import = self::parseJsonAndExtractFiles( $upload, $args['overwrite'] );

			if( isset($import['error']) ){
				return array( 'error' => $import['error'] );
			}
		}

		return true;
	}

	public static function importCollectionsFromLite(){

		if ( empty( self::$_fsCache ) ) {
			self::$_fsCache = FileSystem::get();
		}

		$lite_slug = 'iconpresslite';

		$saved_collections = get_option( $lite_slug . Option::SAVED_COLLECTIONS, [] );
		$default_collection = isset($saved_collections['default']) && !empty($saved_collections['default']) ? $saved_collections['default'] : [];

		$temp_folder = FileSystem::$uploadsDirPath . trailingslashit($lite_slug) . trailingslashit('default');

		if( empty($default_collection) && isset($saved_collections['copied']) && !self::$_fsCache->is_dir( $temp_folder ) ) {
			return;
		}

		if( self::importDataAndCopyFiles($default_collection, $temp_folder, false, false)) {
			update_option( $lite_slug . Option::SAVED_COLLECTIONS, [
				'default' => $default_collection,
				'copied' => true
			] );
		}
	}

}