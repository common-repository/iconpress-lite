<?php

namespace IconPressLite__enshrined\svgSanitize\data;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface TagInterface
 *
 * @package IconPressLite__enshrined\svgSanitize\tags
 */
interface TagInterface
{

    /**
     * Returns an array of tags
     *
     * @return array
     */
    public static function getTags();

}
