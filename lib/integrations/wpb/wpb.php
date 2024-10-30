<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('iconpress/integrations/supported', function ($sup) {
	$sup[] = 'wpb';
	return $sup;
} );



add_action( 'vc_before_init', function () {
	// Load Params
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_browse_icon.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_number.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_list.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_colorpicker.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_title.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_slider.php' );
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/params/iconpress_toggle.php' );

	// Load Elements
    require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/element_iconpress_box/iconpress_box.php' );
    require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/element_iconpress_icon/iconpress_icon.php' );
} );


add_action( 'vc_backend_editor_enqueue_js_css',  '\IconPressLite\Integrations\Base::registerIconPressScripts' );
add_action( 'vc_frontend_editor_enqueue_js_css',  '\IconPressLite\Integrations\Base::registerIconPressScripts' );

add_action( 'vc_backend_editor_enqueue_js_css', 'iconpressWpb_loadAssets', 999 );
add_action( 'vc_frontend_editor_enqueue_js_css', 'iconpressWpb_loadAssets', 999 );

if( !function_exists('iconpressWpb_loadAssets')){
	function iconpressWpb_loadAssets() {
		wp_enqueue_style( 'iconpress-wpb-params-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/wpb/params/assets/params.css', array() );

		wp_enqueue_style( 'iconpress-panel-css' );
		wp_enqueue_script( 'iconpress-panel-js' );
	}
}