<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('iconpress_Wpb_Param_ColorPicker') ){

	class iconpress_Wpb_Param_ColorPicker {

		function __construct(){
			add_action('admin_enqueue_scripts', array(get_class(), 'add_actions'));
			vc_add_shortcode_param( 'iconpress_colorpicker', array($this, 'render'), trailingslashit(ICONPRESSLITE_URI) . 'lib/integrations/wpb/params/assets/params.min.js' );
		}

		public static function add_actions(){
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker-alpha', trailingslashit(ICONPRESSLITE_URI) . 'lib/integrations/wpb/params/assets/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), ICONPRESSLITE_VERSION, true );
		}

		function render( $settings, $value ) {
			return '<div class="iconpress-color-group">'
		       . '<input name="' . $settings['param_name'] . '" data-alpha="true" class="wpb_vc_param_value ' . $settings['param_name'] . ' iconpress-color-control" type="text" value="' . $value . '"/>'
		       . '</div>';
		}
	}

	new iconpress_Wpb_Param_ColorPicker();
}