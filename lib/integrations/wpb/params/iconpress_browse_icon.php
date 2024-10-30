<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('iconpress_Wpb_Param_browseIcon') ){
	class iconpress_Wpb_Param_browseIcon {

		function __construct(){
			vc_add_shortcode_param( 'iconpress_browse_icon', array($this, 'render'), trailingslashit(ICONPRESSLITE_URI) . 'lib/integrations/wpb/params/assets/params.min.js' );
		}

		function render( $settings, $value ) {

			$instance_id = uniqid('ip');

			ob_start();
			?>

			<div class="iconpressWpb-browseIcon-wrapper <?php echo !$value ? 'is-empty' : '' ?>" id="<?php echo $instance_id; ?>">

				<div class="iconpressWpb-browseIcon-iconWrapper">
					<svg class="iconpress-icon js-iconpressWpb-browseIcon-icon" aria-hidden="true" role="img">
						<use href="<?php echo $value ? '#'.$value : ''; ?>" xlink:href="<?php echo $value ? '#'.$value : ''; ?>"></use>
					</svg>
					<span><?php _e('No Icon Selected', 'iconpress') ?></span>
				</div>

				<button class="button iconpressWpb-browseIcon-insert" data-instance-id="<?php echo $instance_id; ?>">
					<span class="iconpressWpb-insertText-add"><?php _e('Add Icon', 'iconpress'); ?></span>
					<span class="iconpressWpb-insertText-change"><?php _e('Change Icon', 'iconpress'); ?></span>
				</button>

				<button class="button iconpressWpb-browseIcon-remove">
					<span><?php _e('Remove', 'iconpress'); ?></span>
				</button>

				<input name="<?php echo $settings['param_name']; ?>" class="wpb_vc_param_value" type="hidden" value="<?php echo esc_attr( $value ); ?>" />

			</div>

			<?php
			return ob_get_clean();

		}


	}

	new iconpress_Wpb_Param_browseIcon();
}