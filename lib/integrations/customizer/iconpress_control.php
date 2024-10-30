<?php

use IconPressLite\Helpers\Option as Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists('WP_Customize_IconPress_Control') && class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Class WP_Customize_IconPress_Control
	 */
	class WP_Customize_IconPress_Control extends WP_Customize_Control
	{

		public $type = "iconpress";

		public function enqueue()
		{
			wp_enqueue_style('iconpress-panel-css');
			wp_enqueue_script( "iconpress-customizer-js", ICONPRESSLITE_URI . "lib/integrations/customizer/js/customizer.js", [ 'iconpress-panel-js' ], ICONPRESSLITE_VERSION, true );
		}

		public function to_json()
		{
			parent::to_json();

			$this->json['label'] = $this->label;

			// @todo: Replace with our own role manager
			$this->json['canUpload'] = current_user_can( 'upload_files' );

			$this->json['button_labels'] = array(
				'select' => __( 'Select Icon', 'iconpress' ),
				'change' => __( 'Change Icon', 'iconpress' ),
				'default' => __( 'Default', 'iconpress' ),
				'remove' => __( 'Remove', 'iconpress' ),
				'placeholder' => __( 'No icon selected', 'iconpress' ),
				'frame_title' => __( 'Select Icon', 'iconpress' ),
				'frame_button' => __( 'Choose Icon', 'iconpress' ),
			);

			$this->json['id'] = $this->id;

			$value = $this->value();

			// Check if the icon exists
			// in our saved collection
			if ( $value ) {
				$exists = 0;
				$saved_collections = get_option( Option::getOptionName( Option::SAVED_COLLECTIONS ), [] );
				if ( is_array( $saved_collections ) && isset( $saved_collections['default'] ) ) {
					$exists = array_filter( $saved_collections['default'], function ( $v, $k ) use ( $value ) {
						return $v['internal_id'] == $value;
					}, ARRAY_FILTER_USE_BOTH );
				}
				if ( count( $exists ) === 0 ) {
					$value = '';
				}
			}

			if ( is_object( $this->setting ) ) {

				if ( $this->setting->default ) {
					$this->json['defaultIcon'] = $this->setting->default;
				}

				if ( $value && $this->setting->default && $value === $this->setting->default ) {
					// Set the default as the icon.
					$this->json['icon'] = $this->json['defaultIcon'];
				}
				elseif ( $value ) {
					$this->json['icon'] = $value;
				}
			}
		}

		public function render_content()
		{
		}

		public function content_template()
		{
			?>

			<#
			var descriptionId = _.uniqueId( 'customize-media-control-description-' );
			var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
			#>

			<div class="iconpress-control iconpress-control--customizer">

				<# if ( data.label ) { #>
				<label class="customize-control-title" for="{{ data.id }}">{{ data.label }}</label>
				<# } #>

				<div class="customize-control-notifications-container"></div>

				<# if ( data.description ) { #>
				<span id="{{ descriptionId }}" class="customize-control-description">{{{ data.description }}}</span>
				<# } #>

				<div class="ip-customizer-view">

					<#

					if ( data.icon !== void 0 && data.icon !== '' ) { #>

					<# if ( data.canUpload ) { #>
					<div class="ip-customizer-preview" data-instance-id="{{ data.id }}">
						<# } else { #>
						<div class="ip-customizer-preview">
							<# } #>
							<svg class="iconpress-icon" aria-hidden="true" role="img">
								<use href="#{{ data.icon }}" xlink:href="#{{ data.icon }}"></use>
							</svg>
						</div>
						<div class="actions">
							<# if ( data.canUpload ) { #>
							<button type="button" class="button remove-button">{{ data.button_labels.remove }}</button>
							<button type="button" class="button upload-button control-focus ip-customizer-insert" data-instance-id="{{ data.id }}" id="{{ data.id }}" {{{ describedByAttr }}}>
								{{ data.button_labels.change }}
							</button>
							<# } #>
						</div>
						<# } else { #>
						<div class="ip-customizer-placeholder">
							{{ data.button_labels.placeholder }}
						</div>
						<div class="ip-customizer-actions">
							<# if ( data.defaultIcon ) { #>
							<button type="button" class="button default-button">{{ data.button_labels['default'] }}
							</button>
							<# } #>
							<# if ( data.canUpload ) { #>
							<button type="button" class="button ip-customizer-insert" data-instance-id="{{ data.id }}" id="{{ data.id }}" {{{ describedByAttr }}}>
								{{ data.button_labels.select }}
							</button>
							<# } #>
						</div>
						<# } #>

					</div>

				</div>
			<?php
		}
	}
}
