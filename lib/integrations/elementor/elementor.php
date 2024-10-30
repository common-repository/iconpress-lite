<?php

namespace IconPressLiteIcon;

use IconPressLiteIcon\Widgets\Widget_IconPress_Icon;
use IconPressLiteIcon\Control\IconPressControl_BrowseIcon;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main IconPressElementor Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
if( !class_exists('IconPressElementor') ){
	class IconPressElementor {

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			add_action( 'plugins_loaded', [$this, 'setup'] );
		}

		public function setup() {
			// Exit if Elementor is not active
			if ( ! did_action( 'elementor/loaded' ) ) {
				return;
			}
			$this->control_includes();
			$this->add_actions();
		}

		/**
		 * Add Actions
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 */
		private function add_actions() {

			add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

			add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

			add_action( 'elementor/editor/before_enqueue_scripts',  '\IconPressLite\Integrations\Base::registerIconPressScripts' );

			// load controls assets
			add_action( 'elementor/editor/before_enqueue_scripts', function() {
				wp_enqueue_style( 'iconpress-elementor-editor-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/elementor/css/editor.css', array('iconpress-panel-css'), ICONPRESSLITE_VERSION );
				wp_enqueue_script( 'iconpress-elementor-controls-js', trailingslashit( ICONPRESSLITE_URI ) . '/lib/integrations/elementor/js/controls.js', [ 'iconpress-panel-js' ], ICONPRESSLITE_VERSION, true );
			}, 10 );

			add_filter('iconpress/integrations/supported', [ $this, 'addSupport' ]);

			// Frontend

			// Register Widget Styles
			add_action( 'elementor/frontend/after_enqueue_styles', function() {
				wp_enqueue_style( 'iconpress-integrations-frontend-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/common/styles.css', array(), ICONPRESSLITE_VERSION );
			}, 10 );

			// Register Widget Scripts
			add_action( 'elementor/frontend/after_register_scripts', function() {
				wp_enqueue_script( 'iconpress-integrations-frontend-js', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/common/scripts.js', [ 'jquery', 'underscore' ], true);
			}, 10 );

		}
		/**
		 * On Widgets Registered
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 */
		public function on_widgets_registered() {
			$this->includes();
			$this->register_widget();
		}
		/**
		 * Includes
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 */
		private function includes() {
			require __DIR__ . '/widget_iconpress_icon/iconpress_icon.php';
		}
		/**
		 * Register Widget
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 */
		private function register_widget() {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widget_IconPress_Icon() );
		}

		public function control_includes() {
			require_once( __DIR__ . '/controls/iconpress_browse_icon.php' );
		}

		public function register_controls() {

			$controls_manager = \Elementor\Plugin::$instance->controls_manager;
			$controls_manager->register_control( 'iconpress_browse_icon', new IconPressControl_BrowseIcon() );
		}

		public function addSupport($sup){
			$sup[] = 'elementor';
			return $sup;
		}
	}
	new IconPressElementor();
}