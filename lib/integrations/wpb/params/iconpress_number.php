<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('iconpress_Wpb_Param_number') ){

	class iconpress_Wpb_Param_number {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_number', array($this, 'render') );
		}

		function render( $settings, $value ) {

			$def = array(
				'min' => '0',
				'max' => '',
				'step' => '1',
			);

			if( !isset($settings['range']) ) $settings['range'] = array();

			$range = wp_parse_args( $settings['range'], $def );

			return '<div class="iconpress-paramsForm"><input type="number" value="'. $value .'" class="wpb_vc_param_value iconpress-paramsForm-input" min="' . esc_attr($range['min']) . '" max="' . esc_attr($range['max']) . '" step="' . esc_attr($range['step']) . '" name="'.esc_attr($settings['param_name']).'" id="'. esc_attr($settings['param_name']) .'" /></div>';
		}


	}

	new iconpress_Wpb_Param_number();
}