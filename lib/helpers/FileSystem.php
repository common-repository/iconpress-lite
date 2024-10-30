<?php

namespace IconPressLite\Helpers;

use IconPressLite\Base;
use IconPressLite__enshrined\svgSanitize\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FileSystem
 *
 * Utility class to access the file system using WP_Filesystem
 *
 * @package IconPressLite\Helpers
 */
class FileSystem
{
	/**
	 * Holds the cached instance of the WP File System class used
	 * @var null|\WP_Filesystem_Base
	 */
	private static $_fsCache = null;

	/**
	 * Holds the name of the temporary file we will attempt to create/read when checking for system access rights
	 * @var string
	 */
	private static $_fs_temp_fileName = '_icon-press.tmp';

	/**
	 * Holds the system path to the uploads directory, with trailing slash
	 * @var string
	 */
	public static $uploadsDirPath = '';

	/**
	 * Holds the iconpress path to the uploads directory, with trailing slash
	 * @var string
	 */
	public static $uploads__IconPressDirPath = '';
	public static $uploads__IconPressCollectionDirPath = '';

	/**
	 * Holds the http path to the uploads directory, with trailing slash
	 * @var string
	 */
	public static $uploadsDirUri = '';

	public static $collection_name = 'default';

	public static $system_icons = 'assets/img/svg-icons.svg';

	/**
	 * Setup the internal vars
	 */
	public static function initVars()
	{
		// Set the permission constants if not already set.
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
		}

		$uploadsDir = wp_upload_dir();
		self::$uploadsDirPath = wp_normalize_path( trailingslashit( $uploadsDir['basedir'] ) );
		self::$uploadsDirUri = trailingslashit( $uploadsDir['baseurl'] );
		self::$uploads__IconPressDirPath = wp_normalize_path( trailingslashit( Base::PLUGIN_SLUG ) );
		self::$uploads__IconPressCollectionDirPath = wp_normalize_path( trailingslashit( self::$collection_name ) );

		self::checkUploadsDir();
	}

	public static function getSpritePath(){
		return self::$uploadsDirPath . self::$uploads__IconPressDirPath . self::$collection_name . '.svg';
	}

	public static function getSpriteUri(){
		return self::$uploadsDirUri . self::$uploads__IconPressDirPath . self::$collection_name . '.svg';
	}

	/**
	 * Check access to File System and display the credentials form if the user doesn't have access to it.
	 */
	public static function checkWpFileSystem()
	{
		global $wp_filesystem;

		//#! Restrict this to only our pages
		if ( false !== stripos( $_SERVER['REQUEST_URI'], Base::PLUGIN_SLUG ) ) {
			if ( ! self::__canAccessFS() ) {
				$url = wp_nonce_url( 'admin.php?page=' . Base::PLUGIN_SLUG, 'iconpress-fs-check' );
				//#! Check credentials
				if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
					self::__renderAdminNotice();
					return; // stop processing here
				}
				//#! Try to use the credentials
				if ( ! WP_Filesystem( $creds ) ) {
					self::__renderAdminNotice();
					/* incorrect connection data - ask for credentials again, now with error message */
					request_filesystem_credentials( $url, '', false, false, null );
					return;
				}
			}
		}
	}

	/**
	 * Retrieve the reference to the instance of the WP_Filesystem_Base class and cache it
	 * @global $wp_filesystem
	 * @return null|\WP_Filesystem_Base
	 */
	public static function get()
	{
		if ( empty( self::$_fsCache ) ) {
			global $wp_filesystem;
			self::$_fsCache = $wp_filesystem;
			if ( empty( self::$_fsCache ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
				require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
				self::$_fsCache = new \WP_Filesystem_Direct( [] );
			}
		}
		return self::$_fsCache;
	}

	/**
	 * Reset the global $wp_filesystem to its original state
	 * @global $wp_filesystem
	 */
	public static function reset()
	{
		if ( ! empty( self::$_fsCache ) ) {
			global $wp_filesystem;
			$wp_filesystem = self::$_fsCache;
		}
	}

	/**
	 * Render the admin notice informing the user we need access to file system
	 */
	private static function __renderAdminNotice()
	{
		?>
		<div class="notice notice-warning">
			<p><?php echo sprintf( __( 'The <strong>%s</strong> plugin needs access to file system in order to function properly.  ', 'iconpress' ), 'IconPress' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Attempt to write/read a temporary file to check if we have access to file system
	 * @return bool
	 */
	private static function __canAccessFS()
	{
		if ( empty( self::$_fsCache ) ) {
			self::get();
		}
		$fp = self::$uploadsDirPath . self::$_fs_temp_fileName;
		if ( ! self::$_fsCache->is_file( $fp ) ) {
			self::$_fsCache->put_contents( $fp, 'test' );
		}
		$c = self::$_fsCache->get_contents( $fp );
		return ( ! empty( $c ) );
	}

	public static function getSingleSvgPath( $internal_id, $full = true )
	{
		if ( empty( self::$_fsCache ) ) {
			self::get();
		}
		return ( $full ? self::$uploadsDirPath : '' ) . self::$uploads__IconPressDirPath . self::$uploads__IconPressCollectionDirPath . $internal_id . '.svg';
	}

	/**
	 * Cleanup the svg file
	 * @param object $svg
	 * @param string $internal_id
	 * @return null|string|string[]
	 */
	public static function __cleanupSvgFile( $svg = '', $internal_id = '', $gen_sprite = false )
	{
		if( is_null($svg) || ! is_object($svg) ){
			return false;
		}

		if ( $svg->attributes()->id ) {
			$svg->attributes()->id = $internal_id;
		}
		else {
			$svg->addAttribute( "id", $internal_id );
		}

		// check for viewport tag and if width and height existis, use them;
		if( ! $svg->attributes()->viewBox ) {
			$def = 100;
			$width = $svg->attributes()->width ? $svg->attributes()->width : $def;
			$height = $svg->attributes()->height ? $svg->attributes()->height : $def;
			$svg->addAttribute( "viewBox", "0 0 $width $height" );
		}

		$svg->attributes()->xmlns = "http://www.w3.org/2000/svg";

		// if method called for generate sprite,
		// override width and height attributes
		// firefox embed svg fix
		if( $gen_sprite ) {
			if ( $svg->attributes()->width ) {
				$svg->attributes()->width = '100%';
			}
			else {
				$svg->addAttribute( "width", '100%' );
			}

			if ( $svg->attributes()->height ) {
				$svg->attributes()->height = '100%';
			}
			else {
				$svg->addAttribute( "height", '100%' );
			}
		}

		// Sanitize
		$svgSanitizer = new Sanitizer();
		$svgSanitizer->removeXMLTag( true );

		// unset tags: metadata,
		$svgExtClass = SvgTagExt::getInstance();

		$svgExtClass->filterAllowedTags( [ 'metadata' ] );
		$svgSanitizer->setAllowedTags( $svgExtClass );

		//#! Debug: Uncomment to verify that $svgExtClass->filterAllowedTags( [ 'metadata' ] ) works
//		$tags = $svgSanitizer->getAllowedTags();
//		error_log( 'METADATA EXISTS: ' . (in_array('metadata', $tags) ? 'yes' : 'no') );

$svg_string = $svgSanitizer->sanitize( $svg->asXML() );

		if ( ! $svg_string ) {
			return false;
		}

		// Extra cleanup
		$remove = [
			'/<!--[^>[]*(\[[^]]*\])?-->/msiU',
			'/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]<svg/msiU',
			'/<\?.*\?>/msiU'
		];

		return preg_replace( $remove, [ '', '<svg',  ], $svg_string );
	}

	public static function __cleanupClassAttributes( $svg, $string = false )
	{

		$svg_string = $svg;

		if ( ! $string ) {
			// get svg as string
			$svg_string = $svg->asXML();
		}
		// find class attributes
		preg_match_all( '/class="([\s\S]*?)"/', $svg_string, $matches );
		// get classes
		$classes = isset( $matches[1] ) ? array_unique( $matches[1] ) : [];
		// replace all instances with uniqid
		foreach ( $classes as $value ) {
			$cls = uniqid( 'cls' );
			// @todo: replace with regex
			$svg_string = str_replace( 'class="' . $value . '"', 'class="' . $cls . '"', $svg_string );
			$svg_string = str_replace( '.' . $value . ',', '.' . $cls . ',', $svg_string );
			$svg_string = str_replace( '.' . $value . '{', '.' . $cls . '{', $svg_string );
			$svg_string = str_replace( '.' . $value . ' {', '.' . $cls . ' {', $svg_string );
		}
		if ( $string ) {
			return $svg_string;
		}

		libxml_use_internal_errors( true );
		// return xml'd svg content
		if ( ! $xml_loaded_string = simplexml_load_string( $svg_string ) ) {
			if ( Utility::getSetting('enable_debug', '0') == '1' ) {
				error_log( var_export( libxml_get_errors(), 1 ) );
			}
			wp_send_json_error( __( 'Error cleaning the SVG\'s CSS classes. Try to skip the icon you just toggled, or try to contact support.', 'iconpress' ) );
		}
		else {
			return $xml_loaded_string;
		}
	}

	public static function deleteSvg( $internal_id = '' )
	{

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		if ( ! empty( $internal_id ) ) {

			$svg = self::getSingleSvgPath( $internal_id );

			if ( self::$_fsCache->is_file( $svg ) ) {
				self::$_fsCache->delete( $svg );
			}

			return true;
		}

		return false;
	}

	public static function checkFile( $file )
	{
		if ( empty( self::$_fsCache ) ) {
			self::get();
		}
		$file = self::$uploadsDirPath . self::$uploads__IconPressDirPath . $file;
		if ( ! empty( $file ) && self::$_fsCache->is_file( $file ) ) {
			return true;
		}
		return false;
	}

	public static function getContent( $file )
	{
		if ( empty( self::$_fsCache ) ) {
			self::get();
		}
		$file = self::$uploadsDirPath . self::$uploads__IconPressDirPath . $file;
		if ( ! empty( $file ) && self::$_fsCache->is_file( $file ) ) {
			if ( $contents = self::$_fsCache->get_contents( $file ) ) {
				return self::__cleanupClassAttributes( $contents, true );
			}
		}
		return false;
	}

	/*
	Method to retrieve the SVG from a specific source (local or remote)
	and return its content (as SVG, not symbol)
	 */
	public static function parseSvg( $icon, $download_url, $download_method, $cleanup = false, $generate_sprite = false )
	{
		if ( empty( $download_url ) ) {
			return false;
		}

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		//#! Get local
		if ( false === stripos( $download_url, 'customers.iconpress.io' ) ) {
			$iconSvg = self::$_fsCache->get_contents( $download_url );
		}
		//#! Get from customers.iconpress.io
		else {
			$request = wp_remote_get( $download_url, [
				'timeout' => 7000,
				'redirection' => 5,
				'sslverify' => false
			] );
			if ( is_wp_error( $request ) ) {
				error_log( 'ERROR DOWNLOAD ICON: ' . $request->get_error_message() );
				return false;
			}

			$data = wp_remote_retrieve_body( $request );
			if ( empty( $data ) ) {
				error_log( 'ERROR DOWNLOAD ICON: no data in body' );
				return false;
			}

			$responseData = json_decode( $data, true );
			if ( empty( $responseData ) || is_scalar( $responseData ) || ! is_array( $responseData ) || ! isset( $responseData['icon_data'] ) ) {
				error_log( 'ERROR DOWNLOAD ICON: Invalid response from customers.iconpress.io.' );
				return false;
			}
			$iconSvg = trim( $responseData['icon_data'] );
		}


		// grab contents of the downloaded SVG
		if ( ! empty( $iconSvg ) ) {

			if ( isset( $icon['is_premium'] ) && $icon['is_premium'] ) {
				// refresh user data
				delete_transient( 'iconpress_iconfinder_user_details' );
			}

			$svg = simplexml_load_string( $iconSvg );

			// cleanup
			if ( $cleanup ) {
				$svg = self::__cleanupClassAttributes( $svg );
			}

			// if it's an svg file, get the content of it & replace id attribute
			if ( $download_method == 'svg_file' ) {
				return self::__cleanupSvgFile( $svg, $icon['internal_id'], $generate_sprite );
			}

			// if it's an svg SPRITE file, get the content of the symbol
			// Used for our internal sprites
			elseif ( $download_method == 'svg_sprite' ) {
				foreach ( $svg->children() as $k => $value ) {
					if ( $k == 'symbol' && $value->attributes()->id == $icon['name'] ) {
						if ( $value->attributes()->id ) {
							$value->attributes()->id = $icon['internal_id'];
						}
						else {
							$value->addAttribute( "id", $icon['internal_id'] );
						}
						$value->attributes()->xmlns = "http://www.w3.org/2000/svg";

						$svg_content = $value->asXML();
						return str_replace( '</symbol', '</svg', str_replace( '<symbol', '<svg', $svg_content ) );
					}
				}
			}

		}
		return false;
	}

	/*
	Method to download svg from a specific URL as single SVG or sprite
	 */
	public static function downloadSvg( $icon = array() )
	{
		if ( empty( $icon ) ) {
			return false;
		}

		$download_info = apply_filters( 'iconpress/download_info', [
			'local' => [
				'download_url' => ICONPRESSLITE_DIR . $icon['download_url'],
				'download_method' => 'svg_sprite'
			],
		], $icon );

		$iconType = ( isset( $icon['type'] ) ? strtolower( $icon['type'] ) : '' );

		if ( ! isset( $download_info[ $iconType ] ) || empty($download_info[ $iconType ]['download_url']) ) {
			return false;
		}

		// download & parse SVG
		$svg_content = self::parseSvg( $icon, $download_info[$iconType]['download_url'], $download_info[$iconType]['download_method'], true );

		//write file
		return self::writeSvgFile( $icon['internal_id'], $svg_content );
	}

	/*
	Method to write the svg (individual) file
	 */
	public static function writeSvgFile( $internal_id, $svg_content )
	{

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		if ( $internal_id && $svg_content ) {

			$file_path = self::getSingleSvgPath( $internal_id );

			$dir_path = dirname( $file_path );

			// make dir if it doesn't exist
			if ( ! self::$_fsCache->is_dir( $dir_path ) ) {
				self::$_fsCache->mkdir( $dir_path );
			}

			if ( self::$_fsCache->put_contents( $file_path, $svg_content ) ) {

				$ip_options = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
				if ( !isset( $ip_options['enable_media_library'] ) || ( isset( $ip_options['enable_media_library'] ) && $ip_options['enable_media_library'] == 1) ){
					self::importWpAttachment( $file_path );
				}
				return true;
			}
		}
		return false;
	}

	public static function importWpAttachment( $file = '' )
	{

		if ( empty( $file ) ) {
			return;
		}

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		$filename = basename( $file );
		$upload_file = wp_upload_bits( $filename, null, self::$_fsCache->get_contents( $file ) );

		if ( ! $upload_file['error'] ) {
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				// 'post_parent' => $parent_post_id,
				'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );
			if ( ! is_wp_error( $attachment_id ) ) {
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );
			}
		}
	}

	public static function generateSvgSprite()
	{

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		$start = '<svg id="iconpress_svg_sprite" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';
		$content = '';
		$end = '</svg>';

		$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), array() );
		if ( isset( $saved_collections['default'] ) && ! empty( $saved_collections['default'] ) ) {

			foreach ( $saved_collections['default'] as $key => $icon ) {
				if ( isset( $icon['local_url'] ) && $download_url = $icon['local_url'] ) {
					// download & parse SVG
					$svg_content = self::parseSvg( $icon, self::$uploadsDirPath . $download_url, 'svg_file', false, true );
					$content .= str_replace( '</svg', '</symbol', str_replace( '<svg', '<symbol', $svg_content ) );
				}
			}
		}

		$final_svg_content = $start . $content . $end;

		$svgSanitizer = new Sanitizer();
		$svgSanitizer->removeXMLTag( true );
		$svgSanitizer->minify(true);
		$final_svg_content = $svgSanitizer->sanitize($final_svg_content);

		if ( self::$_fsCache->put_contents( self::getSpritePath(), $final_svg_content ) ) {
			return true;
		}

		return false;
	}

	public static function cleanupUnusedSvg()
	{

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		$svg_folder_path = self::$uploadsDirPath . self::$uploads__IconPressDirPath . self::$uploads__IconPressCollectionDirPath;

		$current_files = [];
		if ( self::$_fsCache->is_dir( $svg_folder_path ) ) {
			$current_files = self::$_fsCache->dirlist( $svg_folder_path, false, false );
		}

		$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), array() );

		if ( isset( $saved_collections['default'] ) && ! empty( $saved_collections['default'] ) ) {
			foreach ( $saved_collections['default'] as $key => $icon ) {
				if ( isset( $icon['local_url'] ) && $local_icon_url = $icon['local_url'] ) {
					$fpath = basename( $local_icon_url );
					unset( $current_files[$fpath] );
				}
			}
		}

		foreach ( $current_files as $key => $value ) {
			if ( self::$_fsCache->is_file( $svg_folder_path . $key ) ) {
				self::$_fsCache->delete( $svg_folder_path . $key );
			}
		}

		return false;
	}


	public static function checkUploadsDir() {

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		$upDir = self::$uploadsDirPath;
		$iDir = self::$uploads__IconPressDirPath;
		$cDir = self::$uploads__IconPressCollectionDirPath;

		$folders_to_make = [
			$upDir . $iDir,
			$upDir . $iDir . $cDir
		];

		foreach ($folders_to_make as $folder) {
			if( !self::$_fsCache->is_dir( $folder ) ) {
				self::$_fsCache->mkdir( $folder );
			}
		}

		return false;
	}

	public static function deleteUploadsDir() {

		if ( empty( self::$_fsCache ) ) {
			self::get();
		}

		if( self::$_fsCache->is_dir( self::$uploadsDirPath . self::$uploads__IconPressDirPath ) ) {
			self::$_fsCache->delete( self::$uploadsDirPath . self::$uploads__IconPressDirPath, true );
		}

		return false;
	}

}

FileSystem::initVars();
