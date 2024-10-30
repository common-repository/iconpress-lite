<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('iconpress_Wpb_Param_Slider') ){

	class iconpress_Wpb_Param_Slider {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_slider', array($this, 'render'), trailingslashit(ICONPRESSLITE_URI) . 'lib/integrations/wpb/params/assets/params.min.js' );
		}

		function render( $settings, $value ) {

			$uid = rand();

			// Defaults
			$val = !empty($value) ? $value : $settings['default'];
			$range = $settings['range'];

			ob_start();
			?>

			<div class="uimkt-slider uimkt-paramsForm">

				<div class="uimkt-slider-inputWrapper">
					<div class="uimkt-slider-inputSlider"></div>
					<div class="uimkt-slider-inputSlider-field">
						<input name="<?php echo $settings['param_name']; ?>_slider" type="number" class="uimkt-paramsForm-select" min="<?php echo esc_attr($range['min']); ?>" max="<?php echo esc_attr($range['max']); ?>" step="<?php echo esc_attr($range['step']); ?>" value="<?php echo esc_attr( $val ); ?>" />
					</div>
				</div>
				<input name="<?php echo $settings['param_name']; ?>" class="wpb_vc_param_value" type="hidden" value="<?php echo esc_attr( $val ); ?>" />

			</div>


			<?php
			return ob_get_clean();
		}
	}

	new iconpress_Wpb_Param_Slider();
}