<?php

namespace IconPressLite;

use IconPressLite\Helpers\Importer;
use IconPressLite\Helpers\Option;
use IconPressLite\Helpers\RestAPI;
use IconPressLite\Helpers\Portability;
use IconPressLite\Helpers\FileSystem;
use IconPressLite\Helpers\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Base
 *
 * The plugin's base class
 *
 * @package IconPress
 */
class Base
{
	const PLUGIN_SLUG = 'iconpresslite';

	const NONCE_NAME = 'iconpresslite_security';
	const NONCE_ACTION = 'iconpresslite_ajax_action';

	const CAPABILITY = 'iconpresslite_manage_plugin';

	/**
	 * Holds the reference to the instance of this class
	 * @var Base
	 */
	private static $_instance = null;

	/**
	 * Base constructor.
	 */
	private function __construct()
	{
		add_action( 'admin_menu', [ $this, 'wp_hook_admin_menu' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_dequeue_registered_scripts' ], 80000 );

		add_action( 'wp_enqueue_scripts', [ $this, 'loadIconpressCss' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'loadIconpressCss' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'loadIconpressCss' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'loadAdminScripts' ] );

		// Triggered when a user's profile has been updated
		add_action( 'edit_user_profile_update', [ $this, 'updateUserAccessRole' ], 8000, 1 );

		// add our system icons into SVG sprite
		add_action( 'iconpress/svg_icons/system', [ $this, 'addSystemIcons' ] );

		// Load SVG Icons
		add_action( 'admin_footer', [ $this, 'includeBackendSvgIcons' ], 9999 );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'includeBackendSvgIcons' ], 9999 );
		// Load SVG Icons into Frontend (Inline)
		add_action( 'wp_footer', [ $this, 'includeFrontendSvgIcons' ], 9999 );
		add_action( 'wp_head', [ $this, 'addAjaxIcons' ], 9999 );
		// add_action( 'admin_print_footer_scripts', [ $this, '' ], 9999 );

		// Add Iconpress icon
		add_shortcode( 'iconpress', [ $this, 'iconPressShortcode' ] );

		add_action( 'admin_head', [ $this, 'addInlineCSS' ] );

		add_action( 'iconpress/topmenu', [ $this, 'addTopMenu' ] );

		add_filter( 'plugin_action_links', [ $this, 'plugin_action_links_pro' ], 20, 2 );

		add_action( 'wpmu_new_blog', [ $this, 'hook_on_createNewSite' ], 10 );
		add_action( 'delete_blog', [ $this, 'hook_on_deleteSite' ], 10 );

	}

	/**
	 * Retrieve the reference to the instance of this class
	 * @return Base
	 */
	public static function getInstance()
	{
		if ( is_null( self::$_instance ) || ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Load scripts necessary in plugin pages
	 * @param string $hook
	 */
	public function wp_hook_admin_enqueue_scripts( $hook )
	{
		if ( false !== stripos( $hook, self::PLUGIN_SLUG ) ) {
			wp_enqueue_style( 'google-fonts-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,700' );
			wp_enqueue_style( self::PLUGIN_SLUG . '-app-styles', ICONPRESSLITE_URI . 'assets/css/app.css', [], ICONPRESSLITE_VERSION, 'all' );

			// Get Options
			$ip_options = Utility::getSettings();

			if ( isset( $ip_options['grid_listing_default_color'] ) ) {
				$custom_css = '.iconpressApp .ip-collectionIcon svg { color:' . $ip_options['grid_listing_default_color'] . '}';
				wp_add_inline_style( self::PLUGIN_SLUG . '-app-styles', $custom_css );
			}

			$grid_customization_color = isset( $ip_options['grid_customization_color'] ) ? $ip_options['grid_customization_color'] : '#525252';
			$grid_listing_default_size = isset( $ip_options['grid_listing_default_size'] ) ? $ip_options['grid_listing_default_size'] : '32';

			$current_user = wp_get_current_user();
			$min = self::getScriptExtension();

			// Load library scripts
			wp_register_script( self::PLUGIN_SLUG . '-app', ICONPRESSLITE_URI . 'assets/js/app'.$min.'.js', [ 'jquery', 'underscore' ], ICONPRESSLITE_VERSION, true );
			wp_localize_script( self::PLUGIN_SLUG . '-app', 'iconPressConfig', apply_filters('iconpress/iconPressConfig', [

				// Security
				'nonce_name' => self::NONCE_NAME,
				'rest_nonce' => wp_create_nonce( 'wp_rest' ),
				'nonce_value' => wp_create_nonce( self::NONCE_ACTION ),

				// URLS
				'url' => get_site_url(),
				'rest_url' => esc_url_raw( rest_url( RestAPI::ICONPRESS_NAMESPACE ) ),
				'plugin_url' => ICONPRESSLITE_URI,
				'plugin_slug' => self::PLUGIN_SLUG . ( is_multisite() ? get_current_blog_id() : '' ),
				'more_icons_url' => self::goProLink('ip-plugin-addmore'),
				'main_url' => add_query_arg( [ 'page' => self::PLUGIN_SLUG ], admin_url( 'admin.php' ) ),
				'user_id' => $current_user->ID,
				'panes' => apply_filters( 'iconpress/panes', [
					[
						'id' => 'iconPress',
						'title' => __( 'Select Icons', 'iconpress' ),
						'type' => 'local',
						// How many collections to load on each request
						'count_collections' => 3,
						// How many icons to load on each request
						'count_icons' => 100,
						// Collections Endpoint
						'collectionsEndpoint' => rest_url( RestAPI::ICONPRESS_NAMESPACE ) . 'collections',
						// Icons Endpoint
						'iconsEndpoint' => rest_url( RestAPI::ICONPRESS_NAMESPACE ) . 'icons',
						// enable browser cache?
						'cache' => true,
						// show filter?
						'showFilter' => false
					],
				] ),
				'supported' => apply_filters( 'iconpress/integrations/supported', [] ),

				// Various
				'svg_sprite' => FileSystem::getSpriteUri(),
				'system_frontend' => Utility::getSetting('system_frontend', '0'),
				'placeholderImg' => ICONPRESSLITE_URI . 'assets/img/placeholder-icon.svg',
				'debug' => isset( $ip_options['enable_debug'] ) ? $ip_options['enable_debug'] : '0',
				'lock' => isset( $ip_options['enable_lock'] ) ? $ip_options['enable_lock'] : '1',
				'modal_item_color' => $grid_customization_color,
				'grid_icon_size' => $grid_listing_default_size,
				// Translations
				'translations' => self::getTranslations(),
			]) );

			if ( function_exists( 'wp_enqueue_code_editor' ) ) {
				wp_enqueue_code_editor( [
					'type' => 'xml'
				] );
			}

			wp_enqueue_script( self::PLUGIN_SLUG . '-app' );
		}

		if ( false !== stripos( $hook, self::PLUGIN_SLUG . '_insert_icon' ) ) {
			/* Hide WP Stuff */
			$hide_stuff_css = "
			html.wp-toolbar {padding-top: 0 !important;}
			#adminmenumain, #wpadminbar, #wpfooter, #wpbody-content .update-nag, #screen-meta, #screen-meta-links, .wrap > .notice, .wrap > .error {display: none !important;}
			#wpcontent {margin-left: 0 !important; }";
			wp_add_inline_style( 'iconpress-css', $hide_stuff_css );
		}

	}

	public function loadAdminScripts(){

		$min = self::getScriptExtension();

		// CSS & JS for options page
		wp_enqueue_script( 'iconpress-admin-js', ICONPRESSLITE_URI . 'assets/js/admin'.$min.'.js', array( 'jquery', 'wp-color-picker' ) );
		wp_localize_script( 'iconpress-admin-js', 'iconPressOptionsConfig', [
			'nonce_rest' => wp_create_nonce( 'wp_rest' ),
			'export_url' => rest_url( RestAPI::ICONPRESS_NAMESPACE . 'export_collection' ),
			'delete_export_url' => rest_url( RestAPI::ICONPRESS_NAMESPACE . 'delete_export' ),
			'import_url' => rest_url( RestAPI::ICONPRESS_NAMESPACE . 'restore' ),
			'delete_icon_url' => rest_url( RestAPI::ICONPRESS_NAMESPACE . 'delete_icon' ),
			'plugin_slug' => self::PLUGIN_SLUG . ( is_multisite() ? get_current_blog_id() : '' ),
		] );
	}


	/**
	 * App translations
	 */
	public function getTranslations()
	{
		return [
			'MY_COLLECTION' => __( 'My Collection', 'iconpress' ),
			'LOAD_MORE' => __( 'LOAD MORE', 'iconpress' ),
			'READ_MORE' => __( 'Read more', 'iconpress' ),
			'LICENSE' => __( 'License', 'iconpress' ),
			'ICONS_SELECTED' => __( 'ICONS SELECTED', 'iconpress' ),
			'BY' => __( 'by', 'iconpress' ),
			'NO_MORE_COLLECTIONS' => __( 'No more collections.', 'iconpress' ),
			'LOAD_MORE_COLLECTIONS' => __( 'LOAD MORE COLLECTIONS', 'iconpress' ),
			'NO_RESULTS' => __( 'No results.', 'iconpress' ),
			'ADD_MORE_ICONS' => __( 'ADD MORE ICONS', 'iconpress' ),
			'TOTAL_ICONS_SELECTED' => __( 'TOTAL ICONS SELECTED', 'iconpress' ),
			'SAVE' => __( 'SAVE', 'iconpress' ),
			'SAVE_AS_NEW' => __( 'SAVE AS NEW', 'iconpress' ),
			'OR' => __( 'OR', 'iconpress' ),
			'SAVE_ACCESS_YOUR_COLLECTION' => __( 'SAVE AND ACCESS YOUR COLLECTION', 'iconpress' ),
			'TYPE_TO_SEARCH_FOR_ICON' => __( 'Type to search for icon', 'iconpress' ),
			'FILTER' => __( 'FILTER', 'iconpress' ),
			'FILTER_SEARCH' => __( 'FILTER SEARCH', 'iconpress' ),
			'ALL' => __( 'All', 'iconpress' ),
			'ICON_SIZE' => __( 'ICON SIZE', 'iconpress' ),
			'ERROR' => __( 'Error', 'iconpress' ),
			'ERROR_CODE' => __( 'Error Code', 'iconpress' ),
			'COPY' => __( 'COPY', 'iconpress' ),
			'INSERT_SHORTCODE_INTO_EDITOR' => __( 'INSERT SHORTCODE INTO EDITOR', 'iconpress' ),
			'YOUR_CUSTOM_COLLECTION' => __( 'Your custom collection:', 'iconpress' ),
			'EMPTY' => __( 'Your custom collection is empty.', 'iconpress' ),
			'ADD_ICONS' => __( 'Add icons.', 'iconpress' ),
			'SYSTEM_ICONS' => __( 'System icons:', 'iconpress' ),
			'SYSTEM_DESC' => __( 'These icons are used throughout our IconPress plugin (reusability FTW!). Developers can add their own icons too.', 'iconpress' ),
			'CUSTOMIZE_CODE' => __( 'CUSTOMIZE CODE', 'iconpress' ),
			'TITLE_ATTRIBUTE' => __( 'TITLE ATTRIBUTE', 'iconpress' ),
			'COLOR' => __( 'COLOR', 'iconpress' ),
			'SIZE' => __( 'SIZE', 'iconpress' ),
			'PREVIEW' => __( 'PREVIEW', 'iconpress' ),
			'INSERT_SHORTCODE' => __( 'INSERT SHORTCODE', 'iconpress' ),
			'INSERT_ICON' => __( 'INSERT ICON', 'iconpress' ),
			'COPY_CODE' => __( 'CUSTOMIZE & COPY CODE', 'iconpress' ),
			'HELP' => __( 'HELP', 'iconpress' ),
			'REMOVE_BROWSER_CACHE' => __( 'Remove Browser Cache & Reload page', 'iconpress' ),
			'LEAVE_CONFIRM' => __( 'It looks like you have been editing something. If you leave before saving, your changes will be lost.', 'iconpress' ),
			'COLOR_NOTICE' => __( "Please note that some icons cannot be colored. This usually happens because there are fills and stroke colors defined into the icon code. As an alternative you can try to edit the icon's source code.", 'iconpress' ),
			'COLLECTION_SAVED' => __( "Collection saved.", 'iconpress' ),
			'ICON_MADE_BY' => __( "icon, made by ", 'iconpress' ),
			'LICENSED_AS' => __( " licensed as ", 'iconpress' ),
			'ICON_INFORMATION' => __( "ICON INFORMATION:", 'iconpress' ),
			'COPY_ATTRIBUTION' => __( "COPY ATTRIBUTION:", 'iconpress' ),
			'SHOW_SUPPORT' => __( "Show support! Insert the attribution on the page of the icon (for example in the page footer) or on the imprint page. Or, you can place the attribution on the credits/description page of the application.", 'iconpress' ),
			'ICON_INFORMATION_TOOLTIP' => __( "Icon Information", 'iconpress' ),
			'DELETE_ICON' => __( "Delete Icon", 'iconpress' ),
			'OR_GET' => __( "or get", 'iconpress' ),
			'IMPORT_TITLE' => __( "It seems you don't have any icon installed. Please import the default icons (over 4000) by clicking the button below. May take 1-2 minutes.", 'iconpress' ),
			'IMPORT_DEFAULT' => __( "IMPORT DEFAULT ICONS", 'iconpress' ),
		];
	}

	public static function dequeueEditorJs( $hook )
	{
		// remove editor script
		if ( false !== stripos( $hook, self::PLUGIN_SLUG . '_insert_icon' ) && wp_script_is( 'iconpress-editor', 'enqueued' ) ) {
			wp_dequeue_script( 'iconpress-editor' );
		}
	}

	/**
	 * Load scripts on frontend
	 */
	public function loadIconpressCss()
	{
		// Main IconPress Stylesheet
		wp_enqueue_style( 'iconpress-css', ICONPRESSLITE_URI . 'assets/css/iconpress.css', [], ICONPRESSLITE_VERSION );
	}

	/**
	 * Create the plugin's main menu
	 * @see add_action( 'admin_menu')
	 * @see action "wp_hook_admin_menu"
	 * @param string $hook
	 */
	public function wp_hook_admin_menu( $hook = '' )
	{

		$title = __( 'IconPress Lite', 'iconpress' );
		$il_title = __( 'Icon Libraries', 'iconpress' );
		$mc_title = __( 'My Collection', 'iconpress' );
		$opt_title = __( 'Options', 'iconpress' );

		$cap = self::CAPABILITY;

		add_menu_page( $title, $title, $cap, self::PLUGIN_SLUG, [ $this, 'page_render_icon_libraries' ], ICONPRESSLITE_URI . 'assets/img/iconpress-small.svg' );
		add_submenu_page( self::PLUGIN_SLUG, $il_title, $il_title, $cap, self::PLUGIN_SLUG, [ $this, 'page_render_icon_libraries' ] );
		add_submenu_page( self::PLUGIN_SLUG, $mc_title, $mc_title, $cap, self::PLUGIN_SLUG . '_my_collection', [ $this, 'page_render_my_collection' ] );
		add_submenu_page( self::PLUGIN_SLUG, $opt_title, $opt_title, $cap, self::PLUGIN_SLUG . '_options', [ $this, 'page_render_options' ] );

		// Iframe for adding icons
		$inset_page_title = __( 'IconPress Insert Icon', 'iconpress' );
		add_submenu_page( null, $inset_page_title, $inset_page_title, $cap, self::PLUGIN_SLUG . '_insert_icon', [ $this, 'page_render_insert' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'wp_hook_admin_enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'dequeueEditorJs' ], 99 );
	}


	public function page_render_icon_libraries()
	{
		require( ICONPRESSLITE_DIR . 'admin/pages/icon-libraries.php' );
	}

	public function page_render_my_collection()
	{
		require( ICONPRESSLITE_DIR . 'admin/pages/my-collection.php' );
	}

	public function page_render_options()
	{
		wp_enqueue_style( 'wp-color-picker' );
		require( ICONPRESSLITE_DIR . 'admin/pages/options.php' );
	}

	public function page_render_insert()
	{
		require( ICONPRESSLITE_DIR . 'admin/pages/insert_page.php' );
	}

	public static function getSvgIcons()
	{
		// Icons from collection
		$sprite_svg = FileSystem::getSpritePath();
		if ( is_readable( $sprite_svg ) ) {
			return include_once $sprite_svg;
		}
		return '';
	}

	/**
	 * Prints the plugin's default icons in page footer
	 */
	public function addSystemIcons()
	{
		$system_icons_path = ICONPRESSLITE_DIR . FileSystem::$system_icons;
		if ( is_readable( $system_icons_path ) ) {
			return include_once $system_icons_path;
		}
		return '';
	}

	public static function getSystemIcons()
	{
		// add system icons
		echo '<svg id="iconpress_svg_sprite_system" xmlns="http://www.w3.org/2000/svg">';
			ob_start();
			do_action( 'iconpress/svg_icons/system' );
			echo ob_get_clean();
		echo '</svg>';
	}

	public static function includeFrontendSvgIcons()
	{
		if( !is_admin() ) {
			if( Utility::getSetting('load_icons_as', 'ajax') == 'inline'  ) {
				// collection
				self::getSvgIcons();
				// system icons
				if(Utility::getSetting('system_frontend', '0') == '1'){
					self::getSystemIcons();
				}
			}
		}
	}

	public static function includeBackendSvgIcons()
	{
		self::getSvgIcons();
		self::getSystemIcons();
	}

	public function addAjaxIcons(){
		if( !is_admin() && Utility::getSetting('load_icons_as', 'ajax') == 'ajax' ) {

			if( is_readable( FileSystem::getSpritePath() ) ){ ?>
				<script>
				jQuery.get( '<?php echo FileSystem::getSpriteUri() ?>', function( data ) {
					if( jQuery(data).length ) {
						jQuery(data).appendTo(jQuery('body'));
					}
				}, 'html');
				<?php
			}

			if( Utility::getSetting('system_frontend', '0') == '1' ){ ?>
				jQuery.get( '<?php echo ICONPRESSLITE_URI . FileSystem::$system_icons ?>', function( data ) {
					if( jQuery(data) ) {
						jQuery('<svg id="iconpress_svg_sprite_system" xmlns="http://www.w3.org/2000/svg">' + data + '</svg>').appendTo(jQuery('body'));
					}
				}, 'html');
			<?php } ?>
			</script>
			<?php
		}
	}

	/**
	 * Dequeue icons loaded by themes or plugins
	 * See: Plugin Options > Dequeue icons
	 * @see \IconPressLite\Base::__construct()
	 */
	public function wp_dequeue_registered_scripts()
	{
		$option = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
		if ( ! empty( $option ) && isset( $option['dequeue_icons'] ) && ! empty( $option['dequeue_icons'] ) ) {
			$ids = array_map( 'trim', explode( ',', $option['dequeue_icons'] ) );
			if ( $ids ) {
				foreach ( $ids as $handle ) {
					if ( wp_style_is( $handle ) ) {
						wp_dequeue_style( $handle );
					}
				}
			}
		}
	}

	/**
	 * Retrieve the specified variable from the $_POST array
	 * @param string $var
	 * @return null
	 */
	public static function getPostVar( $var )
	{
		return ( isset( $_POST["$var"] ) ? $_POST["$var"] : null );
	}

	public static function wp_roles_array()
	{
		$editable_roles = get_editable_roles();
		$roles = [];
		foreach ( $editable_roles as $role => $details ) {
			$roles[] = [
				'role' => $role,
				'name' => translate_user_role( $details['name'] ),
			];
		}
		return $roles;
	}

	//#!
	public static function hook_on_activate()
	{
		Database\Base::checkTables();
		// Importer::importDefaultData();
		if( ICONPRESSLITE_P ) {
			// Check lite's version saved collection and import it
			Portability::importCollectionsFromLite();
		}
		self::addCustomCapability();
	}

	public static function hook_on_deactivate()
	{
		self::removeCustomCapability();
	}

	public static function uninstallMethods(){
		Database\Base::cleanup();
		Option::deleteAll();
		FileSystem::deleteUploadsDir();
	}

	public static function hook_on_uninstall()
	{
		if( is_multisite() ) {
			$sites = get_sites();
			foreach($sites as $site){
				switch_to_blog( $site->blog_id );
				self::uninstallMethods();
				restore_current_blog();
			}
		}
		else {
			self::uninstallMethods();
		}
	}

	/**
	 * Import IconPress collections & icons if site is added in multisite mode
	 */
	public static function hook_on_createNewSite($blog_id)
	{
		if( self::checkMUandActiveNetwork() ) {
			switch_to_blog( $blog_id );
			Database\Base::checkTables();
			// Importer::importDefaultData();
			self::addCustomCapability();
			restore_current_blog();
		}
	}
	/**
	 * Cleanup IconPress tables if site is removed in multisite mode
	 */
	public static function hook_on_deleteSite($blog_id)
	{
		if( self::checkMUandActiveNetwork() ) {
			switch_to_blog( $blog_id );
			Database\Base::cleanup();
			// FileSystem::deleteUploadsDir();
			restore_current_blog();
		}
	}

	/**
	 * Check to see if this is a multisite & IconPress is Network Activated
	 * @return bool
	 */
	public static function checkMUandActiveNetwork(){
		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if( defined('ICONPRESS_DEV_MODE') ) {
			return (bool) is_plugin_active_for_network( 'iconpress/iconpress.php' );
		}
		return (bool) is_plugin_active_for_network( 'iconpress-lite/iconpress.php' );
	}

	/**
	 * Iconpress Shortcode
	 */
	public function iconPressShortcode( $atts )
	{
		$atts = shortcode_atts( array(
			'id' => '',
			'title' => '',
			'style' => '',
			'class' => '',
			'link' => '',
			'target' => '_self',
			'color' => '',
			'hover_color' => '',
		), $atts, 'iconpress' );

		return IconPress__getSvgIcon( $atts );
	}

	function addInlineCSS()
	{
		echo '<style type="text/css" media="screen">';
		echo '#adminmenu .toplevel_page_iconpress .wp-menu-image img {padding-top:7px;}';
		echo '</style>';
	}

	public static function addCustomCapability()
	{
		$optData = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
		if ( ! isset( $optData['user_roles'] ) ) {
			$optData['user_roles'] = [];
		}
		$editable_roles = array_unique( array_merge( [ 'administrator' ], $optData['user_roles'] ) );
		foreach ( $editable_roles as $role ) {
			$user_role = get_role( $role );
			if ( ! $user_role->has_cap( self::CAPABILITY ) ) {
				$user_role->add_cap( self::CAPABILITY );
			}
		}
	}

	public static function removeCustomCapability()
	{
		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $info ) {
			$user_role = get_role( $role );
			if ( $user_role && $user_role->has_cap( self::CAPABILITY ) ) {
				$user_role->remove_cap( self::CAPABILITY );
			}
		}
	}

	/**
	 * Triggered when a user's profile is updated
	 * @param int $user_id
	 */
	public function updateUserAccessRole( $user_id )
	{
		$allowedRoles = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );
		if ( ! isset( $allowedRoles['user_roles'] ) ) {
			$allowedRoles['user_roles'] = [];
		}
		$editable_roles = array_merge( [ 'administrator' ], $allowedRoles['user_roles'] );

		$cu = new \WP_User( $user_id );
		$roles = $cu->roles;

		foreach ( $roles as $role ) {
			$user_role = get_role( $role );
			if ( in_array( $role, $editable_roles ) ) {
				if ( ! $user_role->has_cap( self::CAPABILITY ) ) {
					$user_role->add_cap( self::CAPABILITY );
				}
			}
			else {
				$user_role->remove_cap( self::CAPABILITY );
			}
		}
	}

	/**
	 * Triggered when plugin options saved.
	 * Update all user roles access
	 * @param array $allowedRoles
	 */
	public function updateUserAccessRoles( $allowedRoles = [] )
	{
		$allowedRoles = array_unique( array_merge( [ 'administrator' ], $allowedRoles ) );
		$editable_roles = get_editable_roles();

		foreach ( $editable_roles as $role => $info ) {
			$user_role = get_role( $role );

			if ( in_array( $role, $allowedRoles ) ) {
				if ( ! $user_role->has_cap( self::CAPABILITY ) ) {
					$user_role->add_cap( self::CAPABILITY );
				}
			}
			else {
				$user_role->remove_cap( self::CAPABILITY );
			}
		}
	}

	/**
	 * Check to see whether or not the $userID's role is allowed to access the plugin
	 * @param int $userID
	 * @return bool
	 */
	public static function isUserAllowed( $userID )
	{
		if ( empty( $userID ) ) {
			return false;
		}
		$user = new \WP_User( $userID );
		return user_can( $user, self::CAPABILITY );
	}

	public function addTopMenu(){

		if( class_exists('\\IconPressLite\\Dashboard\\Base' ) ) {
			return;
		}

		 ?>
		<li class="ip-btnGoPro ip-dropDown">
			<a href="<?php echo self::goProLink('ip-plugin-app'); ?>" target="_blank" class="ip-btn ip-btn--lined ip-btn--blue"><?php _e( 'GET PRO', 'iconpress' ); ?></a>

			<div class="ip-btnGoPro-popover ip-dropDown-popover">
				<div class="ip-btnGoPro-popoverInner ip-dropDown-popoverInner">
					<h3><?php _e( 'Why should you go PRO?', 'iconpress' ); ?></h3>
					<ul class="ip-goPro-featureList">
						<li><?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-check', 'style' => 'opacity:.3' ] ); ?><?php echo sprintf(__( 'Access to over 100.000 icons (in all sorts of colors and shapes) from <a href="%s">IconFinder</a>.', 'iconpress' ), 'https://www.iconfinder.com/?ref=iconpress'); ?></li>
						<li><?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-check', 'style' => 'opacity:.3' ] ); ?><?php _e( 'Upload your own icons.', 'iconpress' ); ?></li>
						<li><?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-check', 'style' => 'opacity:.3' ] ); ?><?php _e( 'Edit Icon\'s code', 'iconpress' ); ?></li>
						<li><?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-check', 'style' => 'opacity:.3' ] ); ?><?php _e( 'Download Icons', 'iconpress' ); ?></li>
						<li><?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-check', 'style' => 'opacity:.3' ] ); ?><?php _e( 'and so much more to come!', 'iconpress' ); ?></li>
					</ul>
					<br>
					<a href="<?php echo self::goProLink('ip-plugin-app'); ?>" target="_blank"><?php _e( 'Get IconPress PRO now!', 'iconpress' ); ?></a>
				</div>
			</div>

		</li>
		<?php
	}

	public static function plugin_action_links_pro( $links, $file ) {

		if ( !ICONPRESSLITE_P && $file == plugin_basename( ICONPRESSLITE_DIR . 'iconpress.php') ) {
			$links[] = '<a href="' . self::goProLink('wp-plugins') . '" target="_blank" class="ip-goPro-pluginsLink">'.esc_html__( 'Go Pro' , 'iconpress').'</a>';
		}

		return $links;
	}

	public static function getScriptExtension(){
		return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	}

	public static function getPb(){

		$pbs = [];
		$pbs[] = did_action( 'elementor/loaded' ) ? 'elementor' : '';
		$pbs[] = class_exists('FLBuilderLoader') ? 'beaver' : '';
		$pbs[] = class_exists('Vc_Manager') ? 'wpb' : '';
		$pbs[] = class_exists('ET_Builder_Plugin') ? 'divi' : '';
		$pbs[] = class_exists('SiteOrigin_Panels') ? 'siteorigin' : '';
		$pbs[] = function_exists( 'is_gutenberg_page' )  ? 'gutenberg' : '';

		return implode( ',', array_filter($pbs) );
	}

	public static function goProLink( $source = 'ip-plugin' ){

		$pbs = self::getPb();
		$utm_term = !empty( $pbs ) ? '&utm_term=' . $pbs : '';

		return esc_url( 'https://iconpress.io/buy/?utm_source='. $source .'&utm_campaign=gopro&utm_medium=wp-dash' . $utm_term );
	}
}

Base::getInstance();
