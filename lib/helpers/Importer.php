<?php

namespace IconPressLite\Helpers;

use IconPressLite\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Importer
 *
 * @package IconPressLite\Helpers
 */
class Importer
{
	/**
	 * Import default icons
	 *
	 * @return bool
	 */
	public static function importDefaultData()
	{
		$fs = FileSystem::get();
		if ( $fs ) {
			//#! Process collections
			$filePath = ICONPRESSLITE_DIR . 'svg/collections.json';
			$collections = [];
			$svgFiles = [];
			if ( $fs->is_file( $filePath ) ) {
				$collections = json_decode( $fs->get_contents( $filePath ), true );
			}
			if ( empty( $collections ) ) {
				return false;
			}

			//#! Import collections
			foreach ( $collections as $info ) {
				if ( false !== Database\Collections::getID( $info['identifier'] ) ) {
					Database\Collections::add(
						$info['identifier'],
						$info['name'],
						$info['version'],
						$info['author_name'],
						$info['author_url'],
						$info['license_name'],
						$info['license_url']
					);
				}
				if ( isset( $info['identifier'] ) ) {
					$filePath = ICONPRESSLITE_DIR . 'svg/' . $info['identifier'] . '/symbol/svg/sprite.symbol.svg';
					if ( ! $fs->is_file( $filePath ) ) {
						continue;
					}
					$svgFiles[$info['identifier']] = $filePath;
				}
			}

			//#! Import icons
			if ( ! empty( $svgFiles ) ) {
				foreach ( $svgFiles as $identifier => $filePath ) {
					$collectionID = Database\Collections::getID( $identifier );
					self::importIconsFromFile( $filePath, $collectionID, $identifier );
				}
			}
		}
		return true;
	}

	/**
	 * Import icons from file
	 * @param string $filePath
	 * @param int $collectionID
	 * @param string $identifier
	 * @return bool|false|int
	 */
	public static function importIconsFromFile( $filePath, $collectionID, $identifier )
	{
		if ( empty( $collectionID ) ) {
			return false;
		}
		$icons = self::__extractSvgIcons( FileSystem::get(), $filePath );
		if ( ! empty( $icons ) ) {
			return Database\Icons::addMultiple( $collectionID, $identifier, $icons );
		}
		return false;
	}

	/**
	 * Extract icons from file
	 * @param \WP_Filesystem_Base $fs
	 * @param string $filePath
	 * @return array
	 */
	private static function __extractSvgIcons( $fs, $filePath )
	{
		$icons = [];

		if ( $fs->is_file( $filePath ) ) {

			$svg = $fs->get_contents( $filePath );
			$svg = simplexml_load_string($svg);

			if ( $svg ) {
				$svg->registerXPathNamespace( 'svg', 'http://www.w3.org/2000/svg' );
				$symbols = $svg->children();
				if ( ! empty( $symbols ) ) {
					foreach ( $symbols as $symbol ) {
						$name = $title = '';

						$atts = $symbol->attributes();
						if ( ! empty( $atts ) ) {
							if ( isset( $atts['id'] ) ) {
								$name = sanitize_title( $atts['id'], 'query' );
							}
						}

						if ( $symbol->count() ) {
							$child = $symbol->children();
							if ( $child->title ) {
								$title = (string)$child->title;
							}
							else {
								$title = $name;
								$title = str_replace( array( '-', '_' ), ' ', $title );
							}
						}

						$icons[] = [
							'name' => $name,
							'title' => $title,
							// 'icon' => $symbol->asXML(),
							'style' => '',
						];
					}
				}
			}
		}
		return $icons;
	}

	/**
	 * @param \WP_REST_Request $wpr
	 * @return array|\WP_Error
	 */
	public static function restAPI_importDefaultData( $wpr )
	{
		if ( $wpr instanceof \WP_REST_Request ) {
			Database\Base::checkTables();
			if( self::importDefaultData() ) {
				wp_send_json_success();
			}
			else {
				wp_send_json_error();
			}
		}
	}
}
