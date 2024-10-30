<?php

namespace IconPressLiteIcon;

use IconPressLiteIcon\Control\IconPressControl_BrowseIcon;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('IconPressElementorControls') ){
	final class IconPressElementorControls {

		public function __construct() {

			// Include plugin files
			$this->includes();

			// Register controls
			add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

		}

		public function includes() {
			require_once( __DIR__ . '/controls/iconpress_browse_icon.php' );
		}

		public function register_controls() {
			$controls_manager = \Elementor\Plugin::$instance->controls_manager;
			$controls_manager->register_control( 'iconpress_browse_icon', new IconPressControl_BrowseIcon() );
		}

	}
	new IconPressElementorControls();
}