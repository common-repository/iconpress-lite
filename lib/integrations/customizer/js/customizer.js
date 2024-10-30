(function($, wpCustomizer, _ ){

	wpCustomizer.controlConstructor["iconpress"] = wpCustomizer.Control.extend({

		instance_id: false,

		ready:function(){
			"use strict";

			var control = this;

			// Shortcut so that we don't have to use _.bind every time we add a callback.
			_.bindAll( control, 'openFrame', 'removeFile', 'restoreDefault' );

			// Bind events, with delegation to facilitate re-rendering.
			control.container.on( 'click keydown', '.ip-customizer-insert', control.openFrame );
			control.container.on( 'click keydown', '.iconpress-control .ip-customizer-preview', control.openFrame );
			control.container.on( 'click keydown', '.remove-button', control.removeFile );
			control.container.on( 'click keydown', '.default-button', control.restoreDefault );

			// on customizer select
			$(window).on('iconpress:select:customizer', function(e){
				// grab the settings
				var settings = e.detail;
				// check for instance
				if( control.instance_id == settings.instance_id ){
					// do the macarena
					control.params.icon = settings.internal_id;
					control.setting.set( settings.internal_id );
					control.renderContent();
				}
			});
		},

		openFrame: function(event){
			if ( wpCustomizer.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault;
			if( $.IconPressApp !== void 0 ) {
				// get the instance
				this.instance_id = $(event.currentTarget).attr('data-instance-id');

				if( this.instance_id !== void 0 ){
					// open the panel
					$.IconPressApp.init( this.instance_id, 'customizer' )
				}
			}
			else {
				console.log('$.IconPressApp not loaded.');
			}
		},

		restoreDefault: function( event ) {
			if ( wpCustomizer.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault;
			// do the macarena
			this.params.icon = this.params.defaultIcon;
			this.setting( this.params.defaultIcon );
			this.renderContent(); // Not bound to setting change when emptying.
		},

		removeFile: function( event ) {
			if ( wpCustomizer.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault;
			// do the macarena
			this.params.icon = '';
			this.setting('');
			this.renderContent(); // Not bound to setting change when emptying.
		}

	});

})(jQuery, wp.customize, _);