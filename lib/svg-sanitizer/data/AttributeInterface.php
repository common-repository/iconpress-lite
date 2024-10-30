<?php

namespace IconPressLite__enshrined\svgSanitize\data;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AttributeInterface
 *
 * @package IconPressLite__enshrined\svgSanitize\data
 */
interface AttributeInterface
{
    /**
     * Returns an array of attributes
     *
     * @return array
     */
    public static function getAttributes();
}
