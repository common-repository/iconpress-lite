<?php

namespace IconPressLite\Helpers;

use IconPressLite__enshrined\svgSanitize\data\AllowedTags;
use IconPressLite__enshrined\svgSanitize\data\TagInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SvgTagExt
 * @package IconPressLite\Helpers
 *
 * Allows the possibility of filtering the allowed tags
 */
class SvgTagExt implements TagInterface
{
	/**
	 * The list of allowed tags
	 * @var array
	 */
	private static $_allowedTags = [];

	/**
	 * @var null|\IconPressLite__enshrined\svgSanitize\data\TagInterface
	 */
	private static $_instance = null;

	/**
	 * SvgTagExt constructor.
	 */
	private function __construct()
	{
		self::$_allowedTags = AllowedTags::getTags();
	}

	/**
	 * @return TagInterface|SvgTagExt
	 */
	public static function getInstance()
	{
		if ( is_null( self::$_instance ) || ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Filter the allowed tags
	 * @param array $removeTags The tags to exclude from the allowed list
	 * @return array
	 */
	public function filterAllowedTags( $removeTags = [] )
	{
		if ( ! empty( $removeTags ) ) {
			$tags = [];
			foreach ( self::$_allowedTags as $tag ) {
				if ( in_array( $tag, $removeTags ) ) {
					continue;
				}
				array_push( $tags, $tag );
			}
			self::$_allowedTags = $tags;
		}
		return self::$_allowedTags;
	}

	/**
	 * Returns an array of tags. Overrides the default AllowedTags::getTags() in order to filter the tags to be removed
	 * @return array
	 */
	public static function getTags()
	{
		return self::$_allowedTags;
	}
}
