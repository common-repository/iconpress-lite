<?php

namespace IconPressLite\Integrations;

use IconPressLite\Helpers\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Insert
 * @package IconPressLite\Insert
 */
class Customizer
{

	function __construct()
	{
		// Register control and dummy options
		add_action( 'customize_register', [ $this, 'ip_customize_register' ] );

		// Customizer preview
		add_action( 'customize_preview_init', [ $this, 'ip_customizer_live_preview' ] );

		add_filter('iconpress/integrations/supported', [ $this, 'addSupport' ]);

		add_action( 'customize_controls_enqueue_scripts',  '\IconPressLite\Integrations\Base::registerIconPressScripts' );

	}

	public static function ip_customizer_live_preview()
	{
		wp_enqueue_script( "iconpress-customizer-live-js", ICONPRESSLITE_URI . "lib/integrations/customizer/js/customizer-live.js", array( 'jquery', 'customize-preview' ), ICONPRESSLITE_VERSION, true );
	}

	public static function ip_customize_register( $wp_customize )
	{
		// Load file
		require_once( ICONPRESSLITE_DIR . 'lib/integrations/customizer/iconpress_control.php' );
		// Register IconPress Control
		$wp_customize->register_control_type( 'WP_Customize_IconPress_Control' );
	}

	public function addSupport($sup){
		$sup[] = 'customizer';
		return $sup;
	}

}

new Customizer;
