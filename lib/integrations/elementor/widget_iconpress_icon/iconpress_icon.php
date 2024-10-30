<?php

namespace IconPressLiteIcon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icon widget.
 *
 * Elementor widget.
 *
 * @since 1.0.0
 */
class Widget_IconPress_Icon extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve icon widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'iconpress_icon';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'IconPress Icon', 'iconpress' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'iconpress-eicon iconpress-logo-icon-dark';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the icon widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Register icon widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', 'iconpress' ),
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'iconpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'iconpress' ),
					'stacked' => __( 'Stacked', 'iconpress' ),
					'framed' => __( 'Framed', 'iconpress' ),
				],
				'default' => 'default',
				'prefix_class' => 'iconPress-style--',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'iconpress' ),
				'type' => 'iconpress_browse_icon',
				'label_block' => true,
				'default' => 'iconpress-logo',
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'iconpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => __( 'Circle', 'iconpress' ),
					'square' => __( 'Square', 'iconpress' ),
				],
				'default' => 'circle',
				'condition' => [
					'view!' => 'default',
				],
				'prefix_class' => 'iconPress-shapeStyle--',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'iconpress' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'iconpress' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'iconpress' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'iconpress' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'iconpress' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'iconpress' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .iconPress-iconEl' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'iconpress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => __( 'Primary Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .iconPress-element-iconWrapper' => 'color: {{VALUE}};',
					'{{WRAPPER}} .iconPress-element-icon' => 'color: {{VALUE}};'
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label' => __( 'Secondary Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.iconPress-style--stacked .iconpress-iconWrapper' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.iconPress-style--framed .iconpress-iconWrapper' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label' => __( 'Size', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-element-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'full_size!' => 'full',
				],
			]
		);



		$this->add_control(
			'full_size',
			[
				'label' => __( 'Fully stretch icon', 'iconpress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'iconpress' ),
				'label_off' => __( 'no', 'iconpress' ),
				'return_value' => 'full',
				'default' => '',
			]
		);

		$this->add_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .iconpress-iconWrapper' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					'view!' => 'default',
				],
			]
		);

		$this->add_control(
			'rotate',
			[
				'label' => __( 'Rotate', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-element-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'iconpress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .iconpress-iconWrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'view' => 'framed',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'iconpress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .iconpress-iconWrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'default',
				],
			]
		);

		$this->add_control(
			'entrance_animation',
			[
				'label' => __( 'Entrance Animation (Special)', 'iconpress' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => __( 'None', 'iconpress' ),
					'slideReveal' => __( 'Slide Reveal', 'iconpress' ),
					'explosionReveal' => __( 'Explosion Reveal', 'iconpress' ),
				],
				'condition' => [
					'_animation' => '',
				],
			]
		);

		$this->add_control(
			'entrance_delay',
			[
				'label' => __( 'Entrance delay', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
						'step' => 50,
					],
				],
				'condition' => [
					'_animation' => '',
					'entrance_animation!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hover',
			[
				'label' => __( 'Icon Hover', 'iconpress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label' => __( 'Primary Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .iconpress-iconWrapper:hover .iconPress-element-icon' => 'color: {{VALUE}};'
				],
			]
		);

		$this->add_control(
			'hover_secondary_color',
			[
				'label' => __( 'Secondary Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.iconPress-style--stacked .iconpress-iconWrapper:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.iconPress-style--framed .iconpress-iconWrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'iconpress' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_deko',
			[
				'label' => __( 'Decorations', 'iconpress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'deko_style',
			[
				'label' => __( 'Style', 'iconpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'iconpress' ),
					'icon' => __( 'Icon', 'iconpress' ),

					// 'Dots' => __( 'Dots', 'iconpress' ),
					// 'stripes' => __( 'Dots', 'iconpress' ),

				],
				'default' => '',
			]
		);

		$this->add_control(
			'deko_icon',
			[
				'label' => __( 'Decoration Icon', 'iconpress' ),
				'type' => 'iconpress_browse_icon',
				'label_block' => true,
				'default' => '',
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_size',
			[
				'label' => __( 'Size', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_color',
			[
				'label' => __( 'Fill Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'color: {{VALUE}};',
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_posX',
			[
				'label' => __( 'Horizontal Position', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => -3,
						'max' => 3,
						'step' => 0.05,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'left: calc( 50% - ( ( {{SIZE}}{{UNIT}} * -1 ) + 0.5em ) );',
				],
				'default' => [
					'unit' => 'em',
					'size' => 0,
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_posY',
			[
				'label' => __( 'Vertical Position', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => -3,
						'max' => 3,
						'step' => 0.05,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'top: calc( 50% - ( ( {{SIZE}}{{UNIT}} * -1 ) + 0.5em ) );',
				],
				'default' => [
					'unit' => 'em',
					'size' => 0,
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_rotate',
			[
				'label' => __( 'Rotate', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_opacity',
			[
				'label' => __( 'Opacity', 'iconpress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .iconPress-deko' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_deko_hover',
			[
				'label' => __( 'Decorations Hover', 'iconpress' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'deko_style!' => '',
				],
			]
		);

		$this->add_control(
			'deko_hover_color',
			[
				'label' => __( 'Hover Color', 'iconpress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover .iconPress-deko' => 'color: {{VALUE}};',
				],
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->add_control(
			'deko_hover_animation',
			[
				'label' => __( 'Hover Animation', 'iconpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
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
				],
				'default' => '',
				'condition' => [
					'deko_style' => 'icon',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'iconPress-iconEl' );

		$this->add_render_attribute( 'icon-wrapper', 'class', 'iconpress-iconWrapper' );

		if ( ! empty( $settings['hover_animation'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		if ( isset($settings['deko_hover_animation']) && ! empty( $settings['deko_hover_animation'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'iconPress-dekoAnimation-' . $settings['deko_hover_animation'] );
		}

		if ( isset($settings['full_size']) && 'full' == $settings['full_size'] ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'iconpress-iconWrapper--full' );
		}


		// IconPress Entrance Animations
		if ( $settings['_animation'] == '' && isset( $settings['entrance_animation'] ) && ! empty($settings['entrance_animation']) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'js-check-viewport entry-' . $settings['entrance_animation'] );
			// delay
			if( isset($settings['entrance_delay']['size']) && !empty( $settings['entrance_delay']['size'] ) ){
				$this->add_render_attribute( 'wrapper', 'data-entry-delay', $settings['entrance_delay']['size'] );
			}
		}


		$icon_tag = 'div';

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'href', $settings['link']['url'] );
			$this->add_render_attribute( 'icon-wrapper', 'class', 'iconpress-iconLink' );
			$icon_tag = 'a';

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$this->add_render_attribute( 'icon-wrapper', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'icon-wrapper', 'rel', 'nofollow' );
			}
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo $icon_tag . ' ' . $this->get_render_attribute_string( 'icon-wrapper' ); ?>>
				<?php

				// Decorations

				if( isset($settings['deko_style']) && !empty($settings['deko_style']) ){

					echo '<div class="iconPress-dekoWrapper">';

						// icon
						if( $settings['deko_style'] == 'icon' ){
							echo IconPress__getSvgIcon(array(
								'id' => $settings['deko_icon'],
								'class' => 'iconPress-deko iconPress-deko-icon'
							));
						}

						// echo '<div class="iconPress-deko iconPress-deko--'. $settings['deko_style'] .'">';
						// echo '</div>';

					echo '</div>';
				}

				?>

				<div class="iconPress-element-iconWrapper">
					<?php
						echo IconPress__getSvgIcon(array(
							'id' => $settings['icon'],
							'class' => 'iconPress-element-icon',
						));
					 ?>
				 </div>

			</<?php echo $icon_tag; ?>>
		</div>
		<?php
	}

	/**
	 * Render icon widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<# var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
				iconTag = link ? 'a' : 'div'; #>
		<div class="iconPress-iconEl">
			<{{{ iconTag }}} class="iconpress-iconWrapper elementor-animation-{{ settings.hover_animation }} iconPress-dekoAnimation-{{ settings.deko_hover_animation }} iconpress-iconWrapper--{{ settings.full_size }}" {{{ link }}}>

				<# if ( settings.deko_style && settings.deko_style != 'none' ) { #>
					<div class="iconPress-dekoWrapper">
						<# if ( settings.deko_style == 'icon' ) { #>
						<svg class="iconpress-icon iconPress-deko iconPress-deko-icon" aria-hidden="true" role="img">
							<use href="#{{ settings.deko_icon }}" xlink:href="#{{ settings.deko_icon }}"></use>
						</svg>
						<# } #>
					</div>
				<# } #>
				<div class="iconPress-element-iconWrapper">
					<svg class="iconpress-icon iconPress-element-icon" aria-hidden="true" role="img">
						<use href="#{{ settings.icon }}" xlink:href="#{{ settings.icon }}"></use>
					</svg>
				</div>
			</{{{ iconTag }}}>
		</div>
		<?php
	}
}
