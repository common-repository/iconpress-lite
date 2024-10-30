<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WpBakeryPB_Box
 * @package IconPressLite\WpBakeryPB_Box
 */
if( !class_exists('WpBakeryPB_Box') && class_exists('WPBakeryShortCode') ) {

	class WpBakeryPB_Box extends WPBakeryShortCode
	{
		private static $_entry_anim = false;

		function __construct()
		{
			add_action( 'vc_after_init', array( $this, 'map' ) );
			add_shortcode( 'vc_iconpress_box', array( $this, 'html_output' ) );
		}

		// Element Mapping
		public function map() {


			$params[] = array(
				'type'        => 'iconpress_browse_icon',
				'heading'     => __( 'Select Icon', 'iconpress' ),
				'param_name'  => 'icon',
				'value'       => '',
				// 'description' => __( 'Click to browse for icon.', 'iconpress' ),
				'admin_label' => false,
			);

			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Alignment', 'iconpress' ),
				'param_name' => 'alignment',
				'options' => array(
					'left' => __( 'Left', 'iconpress' ),
					'center' => __( 'Center', 'iconpress' ),
					'right' => __( 'Right', 'iconpress' ),
					'start' => __( 'Start', 'iconpress' ),
					'end' => __( 'End', 'iconpress' ),
				),
				'value' => 'center',
			);


			$params[] = array(
				'type' => 'vc_link',
				'heading' => __( 'URL (Link)', 'iconpress' ),
				'param_name' => 'link',
			);



			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Icon Style', 'iconpress' ),
				'param_name' => 'main_style',
				'options' => array(
					'' => __( 'Default', 'iconpress' ),
					'stacked' => __( 'Stacked', 'iconpress' ),
					'framed' => __( 'Framed', 'iconpress' ),
				),
				'value' => '',
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Shape Style', 'iconpress' ),
				'param_name' => 'shape_style',
				'options' => array(
					'square' => __( 'Square', 'iconpress' ),
					'circle' => __( 'Circle', 'iconpress' ),
				),
				'value' => 'square',
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('stacked', 'framed')
				),
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Size', 'iconpress' ),
				'param_name' => 'size',
				'value' => '',
				'default' => '70',
				'range' => [
					'min' => 0,
					'max' => 1000,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'full_size',
					'value' => array('')
				),
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_toggle',
				'heading' => __( 'Fully stretch icon', 'iconpress' ),
				'param_name' => 'full_size',
				'toggle' => 'full',
				'value' => '',
				'class' => 'is-inline',
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Color', 'iconpress' ),
				'param_name' => 'color',
				"description" => sprintf(
									__( "Available for path based icons only. <a href='%s' target='_blank'>More info here</a>.", "iconpress" ),
									'https://customers.iconpress.io/kb/icon-is-not-applying-specified-color/' ),
				'group' => __('Styling', 'iconpress'),
			);


			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Shape Color', 'iconpress' ),
				'param_name' => 'shape_color',
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('stacked', 'framed')
				),
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Padding', 'iconpress' ),
				'param_name' => 'ip_padding',
				'value' => '',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 200,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('stacked', 'framed')
				),
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Border Width', 'iconpress' ),
				'param_name' => 'ip_border_width',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 40,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('framed')
				),
				'group' => __('Styling', 'iconpress'),
			);


			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Border Radius', 'iconpress' ),
				'param_name' => 'radius',
				'value' => '',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 300,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('stacked', 'framed')
				),
				'group' => __('Styling', 'iconpress'),
			);


			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Rotate Icon', 'iconpress' ),
				'param_name' => 'rotate',
				'value' => '',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 360,
					'step' => 1,
				],
				'group' => __('Styling', 'iconpress'),
			);




			$params[] = array(
				"type" 			=> 	"iconpress_title",
				"param_name" 	=> 	"hover_title",
				"title" => __( 'Hover options', 'iconpress' ),
				"config" => array(
					'tag' => 'h2',
					'style' => '2',
				),
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Hover Color', 'iconpress' ),
				'param_name' => 'hover_color',
				'group' => __('Styling', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Shape Hover Color', 'iconpress' ),
				'param_name' => 'shape_hover_color',
				"dependency" => array(
					'element' => 'main_style',
					'value' => array('stacked', 'framed')
				),
				'group' => __('Styling', 'iconpress'),
			);


			/**
			 * ADVANCED OPTIONS
			 */

			$params[] = array(
				"type" 			=> 	"iconpress_title",
				"param_name" 	=> 	"decoration_title",
				"title" => __( 'Decorations options', 'iconpress' ),
				"config" => array(
					'tag' => 'h2',
					'style' => '2',
				),
				'group' => __('Advanced', 'iconpress'),
			);


			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Style', 'iconpress' ),
				'param_name' => 'deko_style',
				'options' => array(
					'' => __( 'None', 'iconpress' ),
					'icon' => __( 'Icon', 'iconpress' ),
				),
				'value' => '',
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type'        => 'iconpress_browse_icon',
				'heading'     => __( 'Decoration Icon', 'iconpress' ),
				'param_name'  => 'deko_icon',
				'value'       => '',
				'admin_label' => false,
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Decoration Size', 'iconpress' ),
				'param_name' => 'deko_size',
				'value' => '',
				'default' => '',
				'range' => [
					'min' => 20,
					'max' => 300,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Decoration Color', 'iconpress' ),
				'param_name' => 'deko_color',
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Horizontal Position', 'iconpress' ),
				'param_name' => 'deko_pos_x',
				'default' => '',
				'range' => [
					'min' => -3,
					'max' => 3,
					'step' => 0.05,
				],
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);


			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Vertical Position', 'iconpress' ),
				'param_name' => 'deko_pos_y',
				'default' => '',
				'range' => [
					'min' => -3,
					'max' => 3,
					'step' => 0.05,
				],
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Rotate', 'iconpress' ),
				'param_name' => 'deko_rotate',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 360,
					'step' => 1,
				],
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_slider',
				'heading' => __( 'Opacity', 'iconpress' ),
				'param_name' => 'deko_opacity',
				'default' => '',
				'range' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				"type" 			=> 	"iconpress_title",
				"param_name" 	=> 	"misc_title",
				"title" => __( 'Decoration Hover', 'iconpress' ),
				"config" => array(
					'tag' => 'h2',
					'style' => '2',
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_colorpicker',
				'heading' => __( 'Hover Color', 'iconpress' ),
				'param_name' => 'deko_hover_color',
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Hover Animation', 'iconpress' ),
				'param_name' => 'deko_hover_animation',
				'options' => array(
					'' => __( 'None', 'iconpress' ),
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'push' => 'Push',
					'pop' => 'Pop',
					'bounce-in' => 'Bounce In',
					'bounce-out' => 'Bounce Out',
					'rotate' => 'Rotate',
					'grow-rotate' => 'Grow Rotate',
					'float' => 'Float',
					'sink' => 'Sink',
					'bob' => 'Bob',
					'hang' => 'Hang',
					'buzz' => 'Buzz',
					'buzz-out' => 'Buzz Out',
				),
				'value' => '',
				"dependency" => array(
					'element' => 'deko_style',
					'value' => array('icon')
				),
				'group' => __('Advanced', 'iconpress'),
			);


			$params[] = array(
				"type" 			=> 	"iconpress_title",
				"param_name" 	=> 	"misc_title",
				"title" => __( 'Misc. options', 'iconpress' ),
				"config" => array(
					'tag' => 'h2',
					'style' => '2',
				),
				'group' => __('Advanced', 'iconpress'),
			);


			// Entry animation
			$params[] = array(
				'type' => 'iconpress_list',
				'heading' => __( 'Entry Animation (in viewport)', 'iconpress' ),
				'param_name' => 'entry_anim',
				'options' => array(
					'' => __( 'None', 'iconpress' ),
					'fadein' => __( 'Fade In', 'iconpress' ),
					'fadefromleft' => __( 'Fade From Left', 'iconpress' ),
					'fadefromright' => __( 'Fade From Right', 'iconpress' ),
					'fadefromtop' => __( 'Fade From Top', 'iconpress' ),
					'fadefrombottom' => __( 'Fade From Bottom', 'iconpress' ),
					'zoomin' => __( 'Zoom In', 'iconpress' ),
					'slideReveal' => __( 'Slide Reveal', 'iconpress' ),
					'explosionReveal' => __( 'Explosion Reveal', 'iconpress' ),
				),
				'value' => '',
				'group' => __('Advanced', 'iconpress'),
				'description' => __('Selecting any of the options, will only show this element when entering the viewport (while scrolling).', 'iconpress'),
			);

			$params[] = array(
				'type' => 'iconpress_number',
				'heading' => __( 'Entry delay', 'iconpress' ),
				'param_name' => 'entry_delay',
				'value' => '0',
				'range' => [
					'min' => 0,
					'max' => 2000,
					'step' => 10,
				],
				"dependency" => array('element' => "entry_anim", 'not_empty' => true),
				'group' => __('Advanced', 'iconpress'),
			);

			// $params[] = array(
			// 	"type" 			=> 	"iconpress_title",
			// 	"param_name" 	=> 	"comingsoon",
			// 	"title" => __( 'We\'re preparing lots of new features for this element. Stay tuned!', 'iconpress' ),
			// 	"config" => array(
			// 		'tag' => 'h4',
			// 	),
			// 	'group' => __('Advanced', 'iconpress'),
			// );


			$params[] = array(
				"type"        => 	"textfield",
				"heading"     => 	__( "Title", "iconpress" ),
				"param_name"  => 	"title",
				"description" =>	__( "Accessibility title (won't be visually shown)", "iconpress" ),
				"value"       =>	'',
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'el_id',
				'heading' => __( 'Element ID', 'iconpress' ),
				'param_name' => 'el_id',
				'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'iconpress' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'textfield',
				'heading' => __( 'Extra class name', 'iconpress' ),
				'param_name' => 'el_class',
				'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'iconpress' ),
				'group' => __('Advanced', 'iconpress'),
			);

			$params[] = array(
				'type' => 'css_editor',
				'heading' => __( 'Css', 'iconpress' ),
				'param_name' => 'css',
				'group' => __( 'Design options', 'iconpress' ),
			);

			// Map the block with vc_map()
			vc_map(
				[
					'name' => __('IconPress Box', 'iconpress'),
					'base' => 'vc_iconpress_box',
					'description' => __('Display icons from your custom IconPress collection', 'iconpress'),
					'category' => __('IconPress', 'iconpress'),
					'icon' => 'iconpress-logo-icon',
					'params' => $params
				]
			);

		}


		// Element HTML
		public function html_output( $atts ) {

			// Load assets
			wp_enqueue_style( 'iconpress-integrations-frontend-css', trailingslashit( ICONPRESSLITE_URI ) . 'lib/integrations/common/styles.css', array(), ICONPRESSLITE_VERSION );
			wp_enqueue_script('iconpress-integrations-frontend-js', ICONPRESSLITE_URI . 'lib/integrations/common/scripts.js', ['jquery', 'underscore'], ICONPRESSLITE_VERSION, true );

			$uid = uniqid('el');
			$style_tag = $wrapper_styles = '';

			$_atts = vc_map_get_attributes( 'vc_iconpress_box', $atts );

			// Icon's ID
			$icon['id'] = $_atts['icon'];
			$icon['class'] = 'iconPress-element-icon';

			// Accessibility title
			if( $_atts['title'] ) {
				$icon['title'] = $_atts['title'];
			}

			$main_style = isset($_atts['main_style']) && ! empty($_atts['main_style']) ? esc_attr($_atts['main_style']) : '';
			$shape_style = isset($_atts['shape_style']) && ! empty($_atts['shape_style']) ? 'iconPress-shapeStyle--' . esc_attr($_atts['shape_style']) : '';

			// Styles
			$styles = $_atts['size'] ? 'font-size:' . (int) $_atts['size'] . 'px;' : '';
			$styles .= $_atts['color'] ? 'color:' . $_atts['color'] . ';' : '';
			$styles .= isset($_atts['rotate']) && $_atts['rotate'] != '' ? '-webkit-transform: rotate(' . $_atts['rotate'] . 'deg); transform: rotate(' . $_atts['rotate'] . 'deg);' : '';

			if( $styles ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconPress-element-icon {'. $styles .'}';
			}

			// Shape color
			if( isset($_atts['shape_color']) && $shape_color = $_atts['shape_color'] ) {
				if( $main_style == 'stacked' ){
					$wrapper_styles .= 'background-color:' . $shape_color . ';';
				}
				elseif( $main_style == 'framed' ){
					$wrapper_styles .= 'border-color:' . $shape_color . ';';
				}
			}
			$wrapper_styles .= isset($_atts['ip_padding']) && $_atts['ip_padding'] != '' ? 'padding:' . $_atts['ip_padding'] . 'px;' : '';
			$wrapper_styles .= isset($_atts['ip_border_width']) && $_atts['ip_border_width'] != '' && $main_style == 'framed' ? 'border-width:' . $_atts['ip_border_width'] . 'px;' : '';
			$wrapper_styles .= isset($_atts['radius']) && $_atts['radius'] != '' ? 'border-radius:' . $_atts['radius'] . 'px;' : '';

			if( $wrapper_styles ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconpress-iconWrapper {'. $wrapper_styles .'}';
			}

			// Deco
			$deko_style = $deko_hover_style = '';
			$deko_style .= $_atts['deko_size'] ? 'font-size:' . (int) $_atts['deko_size'] . 'px;' : '';
			$deko_style .= $_atts['deko_color'] ? 'color:' . $_atts['deko_color'] . ';' : '';
			$deko_style .= $_atts['deko_pos_x'] != '' ? 'left: calc( 50% - ( ( ' . $_atts['deko_pos_x'] . 'em * -1 ) + 0.5em ) );' : '';
			$deko_style .= $_atts['deko_pos_y'] != '' ? 'top: calc( 50% - ( ( ' . $_atts['deko_pos_y'] . 'em * -1 ) + 0.5em ) );' : '';
			$deko_style .= $_atts['deko_rotate'] ? 'transform: rotate(' . $_atts['deko_rotate'] . 'deg);' : '';
			$deko_style .= $_atts['deko_opacity'] ? 'opacity:' . $_atts['deko_opacity'] . ';' : '';
			if( $deko_style ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconPress-deko {'. $deko_style .'}';
			}

			// Deco hover
			$deko_hover_style .= $_atts['deko_hover_color'] ? 'color:' . $_atts['deko_hover_color'] . ';' : '';
			if( $deko_hover_style ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconpress-iconWrapper:hover .iconPress-deko {'. $deko_hover_style .'}';
			}


			// Link
			$link['start'] = '';
			$link['end']   = '';

			if( isset($_atts['link']) && !empty($_atts['link']) ){
				$url = vc_parse_multi_attribute( $_atts['link'] );
				if( isset($url['url']) ){
					$link['start'] = '<a href="'. $url['url'] .'" target="'. ( isset($url['target']) ? $url['target'] : '' ) .'" class="iconpress-iconLink">';
					$link['end'] = '</a>';
				}
			}

			// Hover Color
			$hover_style = $wrapper_hover_style = '';

			if( isset($_atts['hover_color']) && $hover_color = $_atts['hover_color'] ) {
				$hover_style .= 'color:'. $hover_color .';';
			}
			if( !empty($hover_style) ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconpress-iconWrapper:hover .iconPress-element-icon {' . $hover_style . '}';
			}

			if( isset($_atts['shape_hover_color']) && $shape_hover_color = $_atts['shape_hover_color'] ) {
				if( $main_style == 'stacked' ){
					$wrapper_hover_style .= 'background-color:' . $shape_hover_color . ';';
				}
				elseif( $main_style == 'framed' ){
					$wrapper_hover_style .= 'border-color:' . $shape_hover_color . ';';
				}
			}
			if( !empty($wrapper_hover_style) ) {
				$style_tag .= '.iconPress-iconEl.' . $uid . ' .iconpress-iconWrapper:hover  {' . $wrapper_hover_style . '}';
			}


			// CSS Tab
			$css_tab_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $_atts['css'], ' ' ), $this->settings['base'], $atts );

			// Entry Animation Markup
			$entryAnimation = !empty($_atts['entry_anim']);

			// Classes
			$classes = [
				'iconPress-iconEl',
				$uid,
				esc_attr($css_tab_class),
				'iconPress-style--' . $main_style,
				$shape_style,
				esc_attr($_atts['el_class']),
				$entryAnimation ? 'js-check-viewport entry-'.$_atts['entry_anim'] : ''
			];

			$attributes = [
				'class="'. implode(' ', $classes) . '"',
				'id="' . ( $_atts['el_id'] ? esc_attr($_atts['el_id']) : $uid ) . '"',
				'style="text-align:'. $_atts['alignment'] .';"',
				$entryAnimation && $_atts['entry_delay'] ? 'data-entry-delay="'.$_atts['entry_delay'].'"' : ''
			];

			ob_start();

			if( $style_tag ) {
				echo '<style type="text/css">'.$style_tag.'</style>';
			}

			echo '<div '. implode(' ', $attributes) .'>';

				$iconpressWrapperClasses = [
					'iconpress-iconWrapper',
					isset($_atts['deko_hover_animation']) && ! empty( $_atts['deko_hover_animation'] ) ? 'iconPress-dekoAnimation-' . $_atts['deko_hover_animation'] : '',
					isset($_atts['full_size']) && $_atts['full_size'] == 'full' ? 'iconpress-iconWrapper--full' : ''
				];

				echo '<div class="' . implode(' ', $iconpressWrapperClasses) . '">';

				if( isset($_atts['deko_style']) && !empty($_atts['deko_style']) ){

					echo '<div class="iconPress-dekoWrapper">';

						// icon
						if( $_atts['deko_style'] == 'icon' ){
							echo IconPress__getSvgIcon(array(
								'id' => $_atts['deko_icon'],
								'class' => 'iconPress-deko iconPress-deko-icon'
							));
						}

						// echo '<div class="iconPress-deko iconPress-deko--'. $_atts['deko_style'] .'">';
						// echo '</div>';

					echo '</div>';
				}

				echo $link['start'];
					echo '<div class="iconPress-element-iconWrapper">';
						echo IconPress__getSvgIcon($icon);
					echo '</div>';
				echo $link['end'];

				echo '</div>';

			echo '</div>';

			return ob_get_clean();

		}

		/**
		 * Check if it's edit mode
		 * @return boolean [description]
		 */
		public static function isEditMode()
		{
			return ( function_exists( 'vc_is_page_editable' ) && vc_is_page_editable() );
		}
	}
	new WpBakeryPB_Box();
}
