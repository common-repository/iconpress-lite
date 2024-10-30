<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('iconpress_Wpb_Param_list') ){

	class iconpress_Wpb_Param_list {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_list', array($this, 'render') );
		}

		function render( $settings, $value ) {

			if ( empty( $settings['options'] ) ) { $settings['options'] = array(); }

			$output = '<div class="uimkt-paramsForm">';

			if( isset( $settings['multiple'] ) && $settings['multiple'] ) {
				$output .= '<select class="wpb_vc_param_value iconpress-paramsForm-select" multiple name="'.$settings['param_name'].'" id="'. $settings['param_name'] .'">';
				foreach ($settings['options'] as $key => $option) {

					$checked = '';
					if(is_array($value)) {
						if(in_array($key, $value)) { $checked = 'selected="selected"'; } else { $checked = ''; }
					}

					$output .= '<option value="'.$key.'" '.$checked.' >'.$option.'</option>';
				}
				$output .= '</select>';
			}
			else {
				$output .= '<select class="wpb_vc_param_value iconpress-paramsForm-select" name="'.$settings['param_name'].'" id="'. $settings['param_name'] .'">';
				foreach ($settings['options'] as $key => $option) {
					$output .= '<option  value="'.$key.'" ' . selected($value, $key, false) . ' >'.$option.'</option>';
				}
				$output .= '</select>';
			}
			$output .= '</div>';

			return $output;
		}


	}

	new iconpress_Wpb_Param_list();
}