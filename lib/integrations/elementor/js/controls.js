(function ($) {
	$(window).on('elementor:init', function () {

		var ControlBaseItemView = elementor.modules.controls.BaseData;

		var ControlIconPressBrowseIconItemView = ControlBaseItemView.extend({

			ui: function () {

				var ui = ControlBaseItemView.prototype.ui.apply(this, arguments);

				ui.iconInsert = '.iconpressElm-browseIcon-insert';
				ui.iconWrapperInsert = '.iconpressElm-browseIcon-iconWrapper';
				ui.iconRemove = '.iconpressElm-browseIcon-remove';

				return ui;
			},

			events: function () {
				return _.extend(ControlBaseItemView.prototype.events.apply(this, arguments), {
					'click @ui.iconInsert': 'openFrame',
					'click @ui.iconWrapperInsert': 'openFrame',
					'click @ui.iconRemove': 'removeIcon'
				});
			},

			onReady: function () {
				var self = this;

				// on iconpress select
				$(window).on('iconpress:select:elementor', function (e) {
					// grab the settings
					var settings = e.detail,
						cid_split = settings.instance_id.split(/-/),
						cid = cid_split[cid_split.length - 1];

					// check for instance
					if (self.model.cid == cid) {
						// do the macarena
						self.setValue(settings.internal_id);
						self.render();
					}
				});

			},

			openFrame: function (event) {
				event.preventDefault;

				if ($.IconPressApp !== void 0) {
					// get the instance
					this.instance_id = $(event.currentTarget).attr('data-instance-id');
					if (!_.isUndefined(this.instance_id)) {
						// open the panel
						$.IconPressApp.init(this.instance_id, 'elementor')
					}
				}
				else {
					console.log('$.IconPressApp not loaded.');
				}
			},

			removeIcon: function (event) {
				event.preventDefault;
				this.setValue('');
				this.render();
			}
		});

		elementor.addControlView('iconpress_browse_icon', ControlIconPressBrowseIconItemView);
	});
})(jQuery);
