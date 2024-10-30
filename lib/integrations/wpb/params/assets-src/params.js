( function( $ ) {
	"use strict";

	if( !window.vc || !vc ) {
		return;
	}

	/**
	 * Browse Icon Param
	 * @type {Object}
	 */
	vc.atts.iconpress_browse_icon = {

		init: function(param, $field) {

			var $this = $field.find( '.iconpressWpb-browseIcon-insert' );
			var $wrapper = $field.find('.iconpressWpb-browseIcon-wrapper');

			$this.on('click', function(event){
				event.preventDefault();
				var instance_id = $(event.currentTarget).attr('data-instance-id');
				// initialize the panel
				$.IconPressApp.init( instance_id, 'wpb' )
			});

			$wrapper.find('.iconpressWpb-browseIcon-remove').on('click', function(event){
				event.preventDefault();
				$wrapper.addClass('is-empty');
				$wrapper.find('.wpb_vc_param_value').val('');
			});

			// on customizer select
			$(window).on('iconpress:select:wpb', function(e){
				// grab the settings
				var settings = e.detail;
				// check for instance
				if( $wrapper.attr('id') == settings.instance_id && typeof settings.internal_id != 'undefined' ){
					// do the macarena
					$wrapper.find('.js-iconpressWpb-browseIcon-icon use').attr({'href': '#' + settings.internal_id, 'xlink:href': '#' + settings.internal_id});
					$wrapper.find('.wpb_vc_param_value').val(settings.internal_id);
					$wrapper.removeClass('is-empty');
				}
			});
		}
	};

	vc.atts.iconpress_colorpicker = {
		init: function(param, $field) {

			$(".iconpress-color-control", $field).each(function(i, el) {

				var $this = $(el);

				$this.wpColorPicker({
					border: false,
					// width: 200,
					change: _.debounce(function() {
						$(this).trigger("change");
					}, 500)
				});
			});
		}
	};

	vc.atts.iconpress_slider = {
		parse: function(param) {
			return this.content().find(".wpb_vc_param_value[name=" + param.param_name + "]").parent().find(".uimkt-slider-inputSlider-field input").val();
		},
		init: function(param, $field) {

			var $this = $field.find( '.uimkt-slider' ),
				$slider      = $this.find( '.uimkt-slider-inputSlider' ),
				$input       = $this.find( '.uimkt-slider-inputSlider-field input' );

			$slider.slider( {
				animate: 'fast',
				min:     parseFloat( $input.attr( 'min' ) ),
				max:     parseFloat( $input.attr( 'max' ) ),
				step:    parseFloat( $input.attr( 'step' ) ),
				value:   ! _.isUndefined( $input.attr( 'value' ) ) && ! _.isEmpty( $input.attr( 'value' ) ) ?  parseFloat( $input.attr( 'value' ) ) : '',
				range:   false
			} );

			if ( $input.length ) {
				$slider.on( 'slide', function( event, ui ) {
					$input.val( ui.value );
				} );
				$input.on( 'change', function(e) {
					if( $input.val() !== '' ) {
						$slider.slider( 'value', $input.val() );
						$input.val( $slider.slider( 'value' ) );
					}
				} );
			}

		}
	};

	vc.atts.iconpress_toggle = {
		parse: function(param) {
			var $field = this.content().find(".uimkt-toggle #" + param.param_name + "_toggle");
			return $field.hasClass('is-active') ? $field.attr('data-toggle') :'' ;
		},
		init: function(param, $field) {
			$(".uimkt-toggle-btn", $field).on('click', function(e) {
				var $el = $(e.currentTarget);
				if( $el.next().val() == $el.attr('data-toggle') ){
					$el.next().val('').trigger('change');
				}
				else {
					$el.next().val($el.attr('data-toggle')).trigger('change');
				}
				$el.toggleClass('is-active');
			});
		}
	};


} )( jQuery );