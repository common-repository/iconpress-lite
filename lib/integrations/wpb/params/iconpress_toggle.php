<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('iconpress_Wpb_Param_Toggle') ){

	class iconpress_Wpb_Param_Toggle {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_toggle', array($this, 'render'), trailingslashit(ICONPRESSLITE_URI) . 'lib/integrations/wpb/params/assets/params.min.js' );
		}

		function render( $settings, $value ) {

			ob_start();
			?>
			<div class="uimkt-toggle">
				<div id="<?php echo $settings['param_name']; ?>_toggle" class="uimkt-toggle-btn <?php echo $settings['toggle'] == $value ? 'is-active' : ''; ?>" data-toggle="<?php echo esc_attr($settings['toggle']); ?>"></div>
				<input name="<?php echo $settings['param_name']; ?>" class="wpb_vc_param_value" type="hidden" value="<?php echo esc_attr( $value ); ?>" />
			</div>
			<?php
			return ob_get_clean();
		}
	}

	new iconpress_Wpb_Param_Toggle();
}