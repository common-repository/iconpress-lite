<?php

namespace IconPressLiteIcon\Control;

use Elementor\Base_Data_Control;

class IconPressControl_BrowseIcon extends Base_Data_Control {

	public function get_type() {
		return 'iconpress_browse_icon';
	}

	public function enqueue() {
		wp_enqueue_style( 'iconpress-elementor-controls-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/elementor/css/controls.css', array() );
	}

	protected function get_default_settings() {
		return [
			'label_block' => true
		];
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>

		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">

				<div class="iconpressElm-browseIcon-wrapper" id="<?php echo esc_attr($control_uid); ?>">

					<div class="iconpressElm-browseIcon-iconWrapper" data-instance-id="<?php echo esc_attr($control_uid);?>">
						<# if( data.controlValue ) { #>
						<svg class="iconpress-icon js-iconpressElm-browseIcon-icon" aria-hidden="true" role="img">
							<use href="#{{ data.controlValue }}" xlink:href="#{{ data.controlValue }}"></use>
						</svg>
						<# } else { #>
						<span><?php _e('No Icon Selected', 'iconpress') ?></span>
						<# } #>
					</div>

					<button class="elementor-button elementor-button-default iconpressElm-browseIcon-insert" data-instance-id="<?php echo esc_attr($control_uid); ?>">
						<# if( !data.controlValue ) { #>
						<span class="iconpressElm-insertText-add"><?php _e('Add Icon', 'iconpress'); ?></span>
						<# } else { #>
						<span class="iconpressElm-insertText-change"><?php _e('Change Icon', 'iconpress'); ?></span>
						<# } #>
					</button>

					<# if( data.controlValue ) { #>
					<button class="elementor-button elementor-button-default elementor-button-warning iconpressElm-browseIcon-remove">
						<span><?php _e('Remove', 'iconpress'); ?></span>
					</button>
					<# } #>

				</div>
			</div>

			<# if ( data.description ) { #>
				<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>

		</div>

		<?php
	}

}
