(function( $ ) {

	$(document).ready(function() {

		$('.ip-colorField').wpColorPicker();

		$('.js-options-export-backup').on('click', function(e){
			e.preventDefault();

			var $el = $(e.currentTarget);

			$el.next().addClass('is-active');
			$el.attr('disabled', 'disabled');

			$.ajax({
				url: iconPressOptionsConfig.export_url,
				method: 'GET',
				cache: false,
				//timeout: 10000,
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', iconPressOptionsConfig.nonce_rest );
				},
				success: function(response) {
					if( response && response.success ) {
						location.href = response.data;

						var fpath = response.data.split('/').reverse()[0];

						$.ajax({
							url: iconPressOptionsConfig.delete_export_url,
							method: 'GET',
							cache: false,
							timeout: 10000,
							data: {
								'filename' : fpath
							},
							beforeSend: function ( xhr ) {
								xhr.setRequestHeader( 'X-WP-Nonce', iconPressOptionsConfig.nonce_rest );
							},
							success: function(response) {
								$el.removeAttr('disabled');
								$el.next().removeClass('is-active');
							},
						});
					}
				}
			});

		});

		$('.js-options-import-backup').on('click', function(e){
			e.preventDefault();

			var file,
				$el = $(e.currentTarget),
				$up = $el.next('input[type="file"]'),
				$fieldset = $el.closest('fieldset'),
				showNotice = function(n, type){
					// add notice
					var notice = $('<div class="ip-noticeComp ip-noticeComp--'+ type +'">'+ n +'</div>');
					$fieldset.prepend( notice );
					// remove notice
					setTimeout(function(){
						notice.fadeOut('fast', function() {
							notice.remove();
						});
					}, 3000);
				},
				resetClasses = function(){
					$el.siblings('.spinner').removeClass('is-active');
					$el.removeAttr('disabled');
					$up.val('');
				}

			// prompt select file
			$up.trigger('click');
			// detect changes in upload field
			$up.one('change', function(e){
				file = $(e.currentTarget).prop('files');

				// check zip
                var theFile = ( typeof(file[0]) === 'undefined' ? null : file[0]);
                if( theFile === null ){
                    showNotice('Not a zip! Please select a .zip file.', 'error');
                    return;
                }
                else if (theFile["type"] !== 'application/zip' && theFile["type"] !== 'application/x-zip-compressed') {
                    showNotice('Not a zip! Please select a .zip file. Found type: ' + theFile['type'], 'error');
                    return;
                }

				$el.siblings('.spinner').addClass('is-active');
				$el.attr('disabled', 'disabled');

				// add form data
				var formData = new FormData( $el.closest('form') );
				formData.append( 'file', theFile );
				formData.append( 'overwrite', $('#overwrite_import').prop('checked') );

				$.ajax({
					url: iconPressOptionsConfig.import_url,
					method: 'POST',
					cache: false,
					// timeout: 10000,
					processData: false,
					contentType: false,
					data: formData,
					beforeSend: function ( xhr ) {
						xhr.setRequestHeader( 'X-WP-Nonce', iconPressOptionsConfig.nonce_rest );
					},
					success: function(response) {if( response && response.data ) {
							showNotice(response.data, 'success');
						}
						resetClasses();
						localStorage.removeItem(iconPressOptionsConfig.plugin_slug + '_myCollection');
					},
					error: function(response) {
						var notice = 'Something went wrong: ';
						if( response && response.responseJSON ) {
							notice += ' Message: ' + response.responseJSON.message;
						}
						showNotice(notice, 'error');
						resetClasses();
					}
				});

			});

		});

		$('.js-btn-uploadSvgFiles').on('click', function(e) {
			e.preventDefault();
			$('.ip-uploadSvgFiles').toggleClass('is-hidden');
		});

		if( typeof Dropzone != 'undefined' ){

			Dropzone.options.jsUploadSvgImage = {
				paramName: "file",
				maxFilesize: 1, // MB
				acceptedFiles: 'image/svg+xml',
				maxFiles: 10,
				uploadMultiple: true,
				success: function(file, success) {

					if( file.xhr.response ) {
						var _response = JSON.parse( file.xhr.response );
						if( _response.success ) {
							$('<li><img src="'+ file.dataURL +'"><span data-id="'+_response.data+'" class="ip-uploadedSvgs-delete">&times;</span></li>').appendTo('#uploadedSvgs');
							$('#uploadedSvgs').removeClass('is-empty');
						}
						else {
							$(file.previewTemplate).find('.dz-error-message').show().find('span').text( _response.data );
							console.log('Failed upload: ' + _response.data);
						}
					}

					localStorage.removeItem(iconPressOptionsConfig.plugin_slug + '_myCollection');
				}
			};
		}

		$('#js-uploadMediaLibrary').on('change', function(event) {
			$('#js-upload-svg-image').find('input[name="uploadml"]').val( $(this).prop('checked') ? 1 : 0 );
		});

		$('body').on('click', '.ip-uploadedSvgs-delete', function(event) {
			event.preventDefault();

			var $el = $(event.currentTarget);

			$.ajax({
				url: iconPressOptionsConfig.delete_icon_url,
				method: 'POST',
				cache: false,
				// timeout: 10000,
				data: {
					'internal_id' : $el.attr( 'data-id' )
				},
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', iconPressOptionsConfig.nonce_rest );
				},
				success: function(response) {
					if( response.success ) {
						$el.closest('li').fadeOut('fast', function() {
							$(this).remove();

							if( ! $('#uploadedSvgs li').length ) {
								$('#uploadedSvgs').addClass('is-empty');
							}

							localStorage.removeItem(iconPressOptionsConfig.plugin_slug + '_myCollection');
						});
					}
				},
			});

		});

		// Re-scan collections
		$('#js-rescanCollections').on('click', function(e) {
			e.preventDefault();
			var slug = iconPressOptionsConfig.plugin_slug;
			// remove local icons cache
			localStorage.removeItem(slug + '_filter_local');
			localStorage.removeItem(slug + '_collections_local_0');
			localStorage.removeItem(slug + '_collections_local_1');
			localStorage.removeItem(slug + '_collections_local_all');
			localStorage.removeItem(slug + '_total_collections_local_0');
			localStorage.removeItem(slug + '_total_collections_local_1');
			localStorage.removeItem(slug + '_total_collections_local_all');

			window.location.href = e.currentTarget.href;
		});

	});

})( jQuery );