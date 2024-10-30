<?php

namespace IconPressLite\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Validator
 * @package IconPressLite\Helpers
 */
class Validator
{
	/**
	 * Validate the options before saving
	 *
	 * @param array $options The list of options to validate. Usually $_POST data
	 * @return array
	 */
	public static function validatePluginOptionsSave( $options = [] )
	{
		$result = array();
		if ( empty( $options ) ) {
			$result[] = esc_html( __( 'No options found.', 'iconpress' ) );
		}
		$nonce = \IconPressLite\Base::NONCE_NAME;
		if ( ! isset( $_POST[$nonce] ) || ! wp_verify_nonce( $_POST[$nonce], \IconPressLite\Base::NONCE_ACTION ) ) {
			$result[] = esc_html( __( 'Invalid nonce.', 'iconpress' ) );
			return $result;
		}

		$gridListingDefaultColor = wp_strip_all_tags( \IconPressLite\Base::getPostVar( 'grid_listing_default_color' ) );
		if ( empty( $gridListingDefaultColor ) ) {
			$result[] = esc_html( __( 'Please specify a value for the grid listing color option.', 'iconpress' ) );
		}

		$gridListingDefaultSize = wp_strip_all_tags( \IconPressLite\Base::getPostVar( 'grid_listing_default_size' ) );
		if ( empty( $gridListingDefaultSize ) ) {
			$result[] = esc_html( __( 'Please specify a value for the grid listing size option.', 'iconpress' ) );
		}

		return $result;
	}

	/**
	 * Ensures the provided $var is not empty
	 * @param mixed $var
	 * @return bool
	 */
	public static function notEmpty( $var )
	{
		return ( ! empty( $var ) );
	}
}
