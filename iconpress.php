<?php
/**
 * Plugin Name: IconPress Lite
 * Plugin URI: https://iconpress.io/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: The new standard in WordPress icon management, through a modern, performant and powerful approach using SVG technology.
 * TextDomain: iconpress
 * DomainPath: /languages
 * Author: IconPress team
 * Version: 1.4.9
 * Author URI: https://iconpress.io/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Skip loading when installing.
 */
if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	return;
}

if ( isset( $_REQUEST['action'] ) && ( 'heartbeat' == strtolower( $_REQUEST['action'] ) ) ) {
	return;
}

define( 'ICONPRESSLITE_VERSION', '1.4.9' );
define( 'ICONPRESSLITE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ICONPRESSLITE_URI', plugin_dir_url( __FILE__ ) );
define( 'ICONPRESSLITE_P', file_exists( ICONPRESSLITE_DIR . 'extend/extend.php') );

/**
 * Check requirements
 */
$IconPressLite_InitErrors = array();
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

//#! Required for svg parser
if ( ! class_exists( 'SimpleXMLElement' ) ) {
	$IconPressLite_InitErrors[] = sprintf( __( 'The <strong>%s</strong> class is not available on your server. Please contact your hosting support and ask them to enable this function for you.', 'iconpress' ), 'SimpleXMLElement' );
}
//#! Required in Option
elseif ( ! class_exists( 'ReflectionClass' ) ) {
	$IconPressLite_InitErrors[] = sprintf( __( 'The <strong>%s</strong> class is not available on your server. Please contact your hosting support and ask them to enable this function for you.', 'iconpress' ), 'ReflectionClass' );
}
//#! [PHP >= v5.4.0] Required PHP min version
elseif ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	$IconPressLite_InitErrors[] = sprintf( __( 'The PHP version <strong>%s</strong> is needed in order to be able to run the <strong>%s</strong> plugin. Please contact your hosting support and ask them to upgrade the PHP version to at least v<strong>%s</strong> for you.', 'iconpress' ),
		'5.4.0', 'IconPress', '5.4.0' );
}
// REST API was included starting with WordPress 4.7.
elseif ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) || ! class_exists( 'WP_REST_Server' ) ) {
	$IconPressLite_InitErrors[] = __( '<strong>WordPress Rest API</strong> is needed in order to be able to run the <strong>IconPress</strong> plugin. Please update WordPress to the latest version, or at least version 4.7 .', 'iconpress' );
}

// If Disable JSON API plugin is installed
if ( is_plugin_active( 'disable-json-api/disable-json-api.php' ) ) {
	$IconPressLite_InitErrors[] = __( '<strong><a href="https://wordpress.org/plugins/disable-json-api/" target="_blank">Disable REST API</a></strong> plugin is installed and active. WordPress Rest API is needed in order to be able to run the <strong>IconPress</strong> plugin. If you intentionally disabled WordPress Rest API by installing this plugin, please drop us an email at hello@iconpress.io and let us know. We want to gather as much feedback as possible to see if we can find a workaround for it.', 'iconpress' );
}

/**
 * Render the notices about the plugin's requirements
 */
if ( ! empty( $IconPressLite_InitErrors ) ) {

	function IconPressLite_showInstallErrors() {
		global $IconPressLite_InitErrors;
		echo '<div class="notice notice-error">';
		foreach ( $IconPressLite_InitErrors as $error ) {
			echo "<p>{$error}</p>";
		}
		echo '<p>' . sprintf( __( '<strong>%s</strong> has been deactivated.', 'iconpress' ), 'IconPress Lite' ) . '</p>';
		echo '</div>';
	}

	add_action( 'admin_notices', 'IconPressLite_showInstallErrors' );

	\deactivate_plugins( 'iconpress-lite/iconpress.php' );
	unset( $_GET['activate'], $_GET['plugin_status'], $_GET['activate-multi'] );

	return;
}

//#! Load core files
require( ICONPRESSLITE_DIR . 'lib/Base.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Utility.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Svg_support.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Generator.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Option.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/FileSystem.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Importer.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/RestAPI.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Validator.php' );
require( ICONPRESSLITE_DIR . 'lib/helpers/Portability.php' );

require( ICONPRESSLITE_DIR . 'lib/db/Base.php' );
require( ICONPRESSLITE_DIR . 'lib/db/Collections.php' );
require( ICONPRESSLITE_DIR . 'lib/db/Icons.php' );

require_once( ICONPRESSLITE_DIR . 'lib/svg-sanitizer/load.php' );
//#! Extends SvgSanitizer, so it must be loaded after
require( ICONPRESSLITE_DIR . 'lib/helpers/SvgTagExt.php' );

// Integrations
require( ICONPRESSLITE_DIR . 'lib/integrations/inc.php' );

register_activation_hook( __FILE__, [ '\\IconPressLite\\Base', 'hook_on_activate' ] );
register_deactivation_hook( __FILE__, [ '\\IconPressLite\\Base', 'hook_on_deactivate' ] );
register_uninstall_hook( __FILE__, [ '\\IconPressLite\\Base', 'hook_on_uninstall' ] );


//#! Upgrade + integrity check
add_action( 'init', [ '\\IconPressLite\\Database\\Base', 'checkTables' ] );
add_action( 'rest_api_init', [ '\\IconPressLite\\Helpers\\RestAPI', 'registerRoutes' ], 190 );

if( ICONPRESSLITE_P ) require( ICONPRESSLITE_DIR . 'extend/extend.php' );