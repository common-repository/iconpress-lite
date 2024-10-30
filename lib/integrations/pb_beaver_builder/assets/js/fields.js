(function($){

	FLBuilder.addHook( 'settings-form-init', function() {

		var iconpressIconFields = $( '.fl-builder-settings:visible .iconpressBB-browseIcon-wrapper' );

		iconpressIconFields.each( function() {

			var element = $( this );

			// add icon
			element.find('.iconpressBB-browseIcon-insert, .iconpressBB-browseIcon-iconWrapper').on('click', function(event){
				event.preventDefault();

				if( $.IconPressApp !== void 0 ) {
					// get the instance
					this.instance_id = $(event.currentTarget).attr('data-instance-id');
					if( !_.isUndefined(this.instance_id) ){
						// open the panel
						$.IconPressApp.init( this.instance_id, 'beaver-builder' )
					}
				}
				else {
					console.log('$.IconPressApp not loaded.');
				}
			});
			// remove icon
			element.find('.iconpressBB-browseIcon-remove').on('click', function(event){
				event.preventDefault();

				element.find('input[type="hidden"]').val('').trigger('change');
				element.find('.js-iconpressBB-browseIcon-icon use').attr({'href': '', 'xlink:href':''});
				element.addClass('is-empty');

			});

			// on iconpress select
			$(window).on('iconpress:select:beaver-builder', function(e){

				// grab the settings
				var settings = e.detail;

				// check for instance
				if( element.attr('id') == settings.instance_id ){
					element.find('input[type="hidden"]').val(settings.internal_id).trigger('change');
					element.find('.js-iconpressBB-browseIcon-icon use').attr({'href': '#'+settings.internal_id, 'xlink:href':'#'+settings.internal_id});
					element.removeClass('is-empty');
				}
			});
		} );
	} );

})(jQuery);
