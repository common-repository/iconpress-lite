<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('iconpress_Wpb_Param_Title') ){

	class iconpress_Wpb_Param_Title {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_title', array($this, 'render') );
		}

		function render( $settings, $value ) {

			if( !isset($settings['config']) ) $settings['config'] = array();

			$config = wp_parse_args( $settings['config'], array(
				'tag' => 'h3',
				'style' => '1',
			) );

			return '<'.$config['tag'].' class="uimkt-titleField uimkt-titleField--st'.$config['style'].'">'.$settings['title'].'</'.$config['tag'].'><input type="hidden" class="wpb_vc_param_value" name="' . $settings['param_name'] . '" value="' . $value . '">' ;
		}
	}

	new iconpress_Wpb_Param_Title();
}