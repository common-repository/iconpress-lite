<?php

/**
 * A class that handles loading custom modules and custom
 * fields if the builder is installed and activated.
 */

define( 'ICONPRESSLITE_BEAVERBUILDER_DIR', ICONPRESSLITE_DIR . 'lib/integrations/pb_beaver_builder/' );
define( 'ICONPRESSLITE_BEAVERBUILDER_URL', ICONPRESSLITE_URI . 'lib/integrations/pb_beaver_builder/' );

if( !class_exists('IconPress_BeaverBuilderLoader') ){
	class IconPress_BeaverBuilderLoader {

		/**
		 * Initializes the class once all plugins have loaded.
		 */
		static public function init() {
			add_action( 'plugins_loaded', __CLASS__ . '::setup_hooks' );
		}

		/**
		 * Setup hooks if the builder is installed and activated.
		 */
		static public function setup_hooks() {
			if ( ! class_exists( 'FLBuilder' ) ) {
				return;
			}

			// Load custom modules.
			add_action( 'init', __CLASS__ . '::load_modules' );

			// Register custom fields.
			add_filter( 'fl_builder_custom_fields', __CLASS__ . '::register_fields' );

			// Enqueue custom field assets.
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_field_assets' );

			add_action( 'wp_footer', __CLASS__ . '::panelMarkup' );

			add_filter('iconpress/integrations/supported', __CLASS__ . '::addSupport');
			
		}

		/**
		 * Loads our custom modules.
		 */
		static public function load_modules() {
			require_once ICONPRESSLITE_BEAVERBUILDER_DIR . 'module_iconpress_icon/iconpress_icon.php';

			require_once ( ICONPRESSLITE_BEAVERBUILDER_DIR . 'fields/slider_field.php' );
		}

		/**
		 * Registers our custom fields.
		 */
		static public function register_fields( $fields ) {
			$fields['iconpress_browse_icon'] = ICONPRESSLITE_BEAVERBUILDER_DIR . 'fields/iconpress_browse_icon.php';
			return $fields;
		}

		/**
		 * Enqueues our custom field assets only if the builder UI is active.
		 */
		static public function enqueue_field_assets() {
			if ( ! FLBuilderModel::is_builder_active() ) {
				return;
			}

			wp_enqueue_style( 'bb-iconpress-browse-field', ICONPRESSLITE_BEAVERBUILDER_URL . 'assets/css/fields.css', array(), '' );
			wp_enqueue_script( 'bb-iconpress-browse-field', ICONPRESSLITE_BEAVERBUILDER_URL . 'assets/js/fields.js', array(), '', true );

			wp_enqueue_style( 'iconpress-panel-css');
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'iconpress-panel-js');
		}

		static public function panelMarkup() {
			if ( ! FLBuilderModel::is_builder_active() ) {
				return;
			}
			IconPressLite\Integrations\Base::addPanelMarkup();
		}

		static public function addSupport($sup){
			$sup[] = 'beaver-builder';
			return $sup;
		}
	}
	IconPress_BeaverBuilderLoader::init();
}