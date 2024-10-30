<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Return SVG markup.
 *
 * @param array $args Parameters needed to display an SVG.
 * @return string SVG markup.
 */
if( !function_exists('IconPress__getSvgIcon') ) {
	function IconPress__getSvgIcon( $args = [] )
	{
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'iconpress' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'id', $args ) ) {
			return __( 'Id Missng.', 'iconpress' );
		}

		// Set defaults.
		$defaults = [
			'id' => '',
			'title' => '',
			'style' => '',
			'class' => '',
			'link' => '',
			'target' => '_self',
			'color' => '',
			'hover_color' => '',
		];

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$attributes['aria-hidden'] = 'aria-hidden="true"';

		// Set ARIA.
		if ( $args['title'] ) {
			$attributes['aria-hidden'] = '';
			$unique_id = uniqid();
			$attributes['aria-labelledby'] = 'aria-labelledby="title-' . $unique_id . '"';
		}

		if ( $args['style'] ) {
			$attributes['style'] = 'style="' . $args['style'] . '"';
		}

		$attributes['role'] = 'role="img"';

		$svg = '';
		$svgColorLink = '';

		if( $args['link'] ) {
			// add custom mouse attributes so we can handle hover
			$svgColorLink = 'onmouseover="this.style.color=\''. esc_attr($args['hover_color']) .'\'" onmouseout="this.style.color=\''. esc_attr($args['color']) .'\'"';
			$svg .= '<a class="iconpress-iconLink" href="'.esc_url($args['link']).'" target="'.esc_attr($args['target']).'">';
		}

		// Begin SVG markup.
		$svg .= '<svg class="iconpress-icon iconpress-icon-' . esc_attr( $args['id'] ) . ' ' . esc_attr( $args['class'] ) . '"' . implode( ' ', $attributes ) . ' '. $svgColorLink .'>';

		// Display the title.
		if ( $args['title'] ) {
			$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';
		}

		$svg .= ' <use href="#' . esc_html( $args['id'] ) . '" xlink:href="#' . esc_html( $args['id'] ) . '"></use> ';

		$svg .= '</svg>';

		if( $args['link'] ) {
			$svg .= '</a>';
		}

		return $svg;
	}
}

if( !function_exists('IconPress__getCustomizerMod') ) {
	function IconPress__getCustomizerMod( $mod = '', $echo = false )
	{
		$return = '<div class="iconpressIconMod" data-mod="' . $mod . '">' . IconPress__getSvgIcon( array( 'id' => get_theme_mod( $mod ) ) ) . '</div>';

		if ( $echo ) {
			echo $return;
		}
		return $return;
	}
}
