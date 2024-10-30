( function( $ ) {

	// @todo: properly document
	// eg: echo IconPress__getCustomizerMod( 'iconpress_select_icon' );
	$('.iconpressIconMod').each(function(index, el) {

		var $el = $(el);
		var option = $el.attr('data-mod');

		wp.customize( option, function( value ) {
			value.bind( function( newval ) {
				$el.find('use').attr({'href': '#' + newval, 'xlink:href': '#' + newval});
			} );
		} );
	});

	// @todo: properly document
	// eg: echo IconPress__getSvgIcon( array( 'id' => get_theme_mod('iconpress_select_icon') ) );
	// wp.customize( 'iconpress_select_icon', function( value ) {
	// 	value.bind( function( newval ) {
	// 		$('.iconpress_select_icon').find('use').attr('href', '#' + newval);
	// 	} );
	// } );

} )( jQuery );
