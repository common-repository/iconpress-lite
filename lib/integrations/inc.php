<?php
use IconPressLite\Helpers\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check if enabled
$ip_options = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
$integrations = isset($ip_options['integrations']) ? $ip_options['integrations'] : ['customizer', 'wpbakery', 'elementor', 'beaver-builder', 'gutenberg'];

require_once( ICONPRESSLITE_DIR . 'lib/integrations/Base.php' );

// Customizer
if( in_array('customizer', $integrations) ){
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/customizer/customizer.php' );
}

// WPBakery Page Builder
if( in_array('wpbakery', $integrations) && is_plugin_active( 'js_composer/js_composer.php' ) ){
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/wpb/wpb.php' );
}

// Elementor Page Builder
if( in_array('elementor', $integrations) && is_plugin_active( 'elementor/elementor.php' ) ){
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/elementor/elementor.php' );
}

// Beaver Builder
if( in_array('beaver-builder', $integrations) ){
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/pb_beaver_builder/index.php' );
}

// Gutenberg
if( in_array('gutenberg', $integrations) ){
	require_once( ICONPRESSLITE_DIR . 'lib/integrations/gutenberg/index.php' );
}