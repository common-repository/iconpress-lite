<?php

namespace IconPressLite\Integrations;

use IconPressLite\Base as IpBase;
use IconPressLite\Helpers\FileSystem;
use IconPressLite\Helpers\Option;
use IconPressLite\Helpers\RestAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base
 *
 * Utility class to handle all Integrations operations from within our plugin
 * @package IconPressLite\Integrations
 */
class Base
{
	private static $_markup_loaded = false;

	public function __construct()
	{
		// register iconpress's panel scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'registerIconPressScripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'registerIconPressScripts' ] );

		// load panel markup
		add_action( 'admin_print_footer_scripts', [ $this, 'addPanelMarkup' ], 99 );
	}


	public static function registerIconPressScripts()
	{
		$ip_options = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
		$enable_debug = isset( $ip_options['enable_debug'] ) ? $ip_options['enable_debug'] : '0';

		// CSS
		wp_enqueue_style( 'iconpress-panel-css', ICONPRESSLITE_URI . 'assets/css/panel.css', [], ICONPRESSLITE_VERSION );

		$min = IpBase::getScriptExtension();
		wp_register_script( 'iconpress-panel-js', ICONPRESSLITE_URI . 'assets/js/iconpress'.$min.'.js', [ 'jquery', 'underscore' ], ICONPRESSLITE_VERSION, true );
		wp_localize_script( 'iconpress-panel-js', 'iconPressAppConfig', [
			'nonce_value' => wp_create_nonce( IpBase::NONCE_ACTION ),
			'nonce_name' => IpBase::NONCE_NAME,
			'nonce_rest' => wp_create_nonce( 'wp_rest' ),

			// URLS
			'url' => get_site_url(),
			'rest_url' => esc_url_raw( rest_url( RestAPI::ICONPRESS_NAMESPACE ) ),
			'plugin_url' => ICONPRESSLITE_URI,
			'panel_url' => wp_nonce_url( admin_url( 'admin.php?page=' . IpBase::PLUGIN_SLUG . '_insert_icon' ), 'open_insert_panel', 'ip_nonce' ),
			'svg_sprite' => FileSystem::getSpriteUri(),

			// Various
			'debug' => $enable_debug,
			'insert_icon_button' => self::getWpEditorOption($ip_options),

			// Translations
			'translations' => [
				'INSERT_ICON' =>__( 'Insert Icon', 'iconpress' ),
			],
		] );
		wp_enqueue_script( 'iconpress-panel-js');
	}

	private static function getWpEditorOption($ip_options){
		//#! Hide the button if the user's role is not on the allowed list
		$enable_wpeditor_btn = '0';

		global $post;
		$post_type = ( isset( $post->post_type ) ) ? $post->post_type : '';
		$excludes = apply_filters( 'iconpress/insert/shortcode_exclude', [] );

		if ( IpBase::isUserAllowed( get_current_user_id() ) && ! in_array( $post_type, $excludes ) ) {
			$enable_wpeditor_btn = isset( $ip_options['enable_wpeditor_btn'] ) ? $ip_options['enable_wpeditor_btn'] : '1';
		}

		return $enable_wpeditor_btn;
	}

	public static function addPanelMarkup()
	{
		if ( current_user_can( 'upload_files' ) ) {

			$canUpload = true;

			// when in customizer mode, this function is not found
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( isset( $screen->base ) && $screen->base == 'admin_page_' . IpBase::PLUGIN_SLUG . '_insert_icon' ) {
					$canUpload = false;
				}
			}

			if ( $canUpload && !self::$_markup_loaded ) {

				echo '
				<script type="text/html" id="tmpl-iconpress-panel">
					<div class="ip-insertPanel" id="ip-insertPanel-<%- instance_id %>" data-instance-id="<%- instance_id %>">
						<div class="ip-insertPanel-overlay"></div>
						<div class="ip-insertPanel-inner" id="ip-insertPanel-inner">
							<a href="#" class="ip-insertPanel-close" title="' . esc_attr( __( 'Close Panel', 'iconpress' ) ) . '">' . \IconPress__getSvgIcon( array( 'id' => 'iconpress-icon-close-thin' ) ) . '</a>
						</div>
					</div>
				</script>
				';
				self::$_markup_loaded = true;
			}
		}
		return true;
	}
}
new Base;
