<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WpBakeryPB_Icon
 * @package IconPressLite\WpBakeryPB_Icon
 */
if (  !class_exists('WpBakeryPB_Icon') && class_exists( 'WPBakeryShortCode' ) ) {

	class WpBakeryPB_Icon extends WPBakeryShortCode
	{
		private static $_entry_anim = false;

		function __construct()
		{
			add_action( 'vc_after_init', array( $this, 'map' ) );
			add_shortcode( 'vc_iconpress_icon', array( $this, 'html_output' ) );
		}

		// Element Mapping
		public function map()
		{

			vc_map(
				[
					'name' => __( 'IconPress Icon', 'iconpress' ),
					'base' => 'vc_iconpress_icon',
					'description' => __( '"Icon" element clone, but from your IconPress collection', 'iconpress' ),
					'category' => __( 'IconPress', 'iconpress' ),
					'icon' => 'iconpress-logo-icon-dark',
					'params' => array(

						array(
							'type' => 'iconpress_browse_icon',
							'heading' => __( 'Select Icon', 'iconpress' ),
							'param_name' => 'icon',
							'value' => '',
							// 'description' => __( 'Click to browse for icon.', 'iconpress' ),
							'admin_label' => false,
						),

						array(
							'type' => 'dropdown',
							'heading' => __( 'Icon color', 'iconpress' ),
							'param_name' => 'color',
							'value' => array_merge( getVcShared( 'colors' ), array( __( 'Custom color', 'iconpress' ) => 'custom' ) ),
							'description' => __( 'Select icon color.', 'iconpress' ),
							'param_holder_class' => 'vc_colored-dropdown',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Custom color', 'iconpress' ),
							'param_name' => 'custom_color',
							'description' => __( 'Select custom icon color.', 'iconpress' ),
							'dependency' => array(
								'element' => 'color',
								'value' => 'custom',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Background shape', 'iconpress' ),
							'param_name' => 'background_style',
							'value' => array(
								__( 'None', 'iconpress' ) => '',
								__( 'Circle', 'iconpress' ) => 'rounded',
								__( 'Square', 'iconpress' ) => 'boxed',
								__( 'Rounded', 'iconpress' ) => 'rounded-less',
								__( 'Outline Circle', 'iconpress' ) => 'rounded-outline',
								__( 'Outline Square', 'iconpress' ) => 'boxed-outline',
								__( 'Outline Rounded', 'iconpress' ) => 'rounded-less-outline',
							),
							'description' => __( 'Select background shape and style for icon.', 'iconpress' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Background color', 'iconpress' ),
							'param_name' => 'background_color',
							'value' => array_merge( getVcShared( 'colors' ), array( __( 'Custom color', 'iconpress' ) => 'custom' ) ),
							'std' => 'grey',
							'description' => __( 'Select background color for icon.', 'iconpress' ),
							'param_holder_class' => 'vc_colored-dropdown',
							'dependency' => array(
								'element' => 'background_style',
								'not_empty' => true,
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Custom background color', 'iconpress' ),
							'param_name' => 'custom_background_color',
							'description' => __( 'Select custom icon background color.', 'iconpress' ),
							'dependency' => array(
								'element' => 'background_color',
								'value' => 'custom',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Size', 'iconpress' ),
							'param_name' => 'size',
							'value' => array_merge( getVcShared( 'sizes' ), array( __( 'Extra Large', 'iconpress' ) => 'xl' ) ),
							'std' => 'md',
							'description' => __( 'Icon size.', 'iconpress' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Icon alignment', 'iconpress' ),
							'param_name' => 'align',
							'value' => array(
								__( 'Left', 'iconpress' ) => 'left',
								__( 'Right', 'iconpress' ) => 'right',
								__( 'Center', 'iconpress' ) => 'center',
							),
							'description' => __( 'Select icon alignment.', 'iconpress' ),
						),
						array(
							'type' => 'vc_link',
							'heading' => __( 'URL (Link)', 'iconpress' ),
							'param_name' => 'link',
							'description' => __( 'Add link to icon.', 'iconpress' ),
						),
						vc_map_add_css_animation(),
						array(
							'type' => 'el_id',
							'heading' => __( 'Element ID', 'iconpress' ),
							'param_name' => 'el_id',
							'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'iconpress' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
						),
						array(
							'type' => 'textfield',
							'heading' => __( 'Extra class name', 'iconpress' ),
							'param_name' => 'el_class',
							'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'iconpress' ),
						),
						array(
							'type' => 'css_editor',
							'heading' => __( 'CSS box', 'iconpress' ),
							'param_name' => 'css',
							'group' => __( 'Design Options', 'iconpress' ),
						),

					)
				]
			);

		}


		// Element HTML
		public function html_output( $atts )
		{

			$icon = $color = $custom_color = $background_style = $background_color = $custom_background_color = $size = $align = $el_class = $el_id = $link = $css_animation = $css = $rel = '';

			$atts = vc_map_get_attributes( 'vc_iconpress_icon', $atts );
			extract( $atts );

			$class_to_filter = '';
			$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
			$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

			$url = vc_build_link( $link );
			$has_style = false;
			if ( strlen( $background_style ) > 0 ) {
				$has_style = true;
				if ( false !== strpos( $background_style, 'outline' ) ) {
					$background_style .= ' vc_icon_element-outline'; // if we use outline style it is border in css
				}
				else {
					$background_style .= ' vc_icon_element-background';
				}
			}

			$iconClass = 'xxxx';
			// $iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';

			$style = '';
			if ( 'custom' === $background_color ) {
				if ( false !== strpos( $background_style, 'outline' ) ) {
					$style = 'border-color:' . $custom_background_color;
				}
				else {
					$style = 'background-color:' . $custom_background_color;
				}
			}
			$style = $style ? ' style="' . esc_attr( $style ) . '"' : '';
			$rel = '';
			if ( ! empty( $url['rel'] ) ) {
				$rel = ' rel="' . esc_attr( $url['rel'] ) . '"';
			}
			$wrapper_attributes = array();
			if ( ! empty( $el_id ) ) {
				$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
			}

			ob_start();

			?>
			<div <?php echo implode( ' ', $wrapper_attributes ); ?>
				class="vc_icon_element vc_icon_element-outer<?php echo strlen( $css_class ) > 0 ? ' ' . trim( esc_attr( $css_class ) ) : ''; ?> vc_icon_element-align-<?php echo esc_attr( $align );
				if ( $has_style ) {
					echo ' vc_icon_element-have-style';
				} ?>">
				<div class="vc_icon_element-inner vc_icon_element-color-<?php echo esc_attr( $color );
				if ( $has_style ) {
					echo ' vc_icon_element-have-style-inner';
				} ?> vc_icon_element-size-<?php echo esc_attr( $size ); ?> vc_icon_element-style-<?php echo esc_attr( $background_style ); ?> vc_icon_element-background-color-<?php echo esc_attr( $background_color ); ?>"<?php echo $style ?>>

					<?php
					echo IconPress__getSvgIcon( array(
						'id' => $icon,
						'class' => 'vc_icon_element-icon iconPress-element-icon',
						'style' => 'custom' === $color ? 'color:' . esc_attr( $custom_color ) . ' !important' : ''
					) );
					?>

					<?php
					if ( strlen( $link ) > 0 && strlen( $url['url'] ) > 0 ) {
						echo '<a class="vc_icon_element-link" href="' . esc_attr( $url['url'] ) . '" ' . $rel . ' title="' . esc_attr( $url['title'] ) . '" target="' . ( strlen( $url['target'] ) > 0 ? esc_attr( $url['target'] ) : '_self' ) . '"></a>';
					}
					?></div>
			</div>

			<?php
			return ob_get_clean();

		}

	}

	new WpBakeryPB_Icon();
}
