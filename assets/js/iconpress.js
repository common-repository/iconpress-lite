(function($) {
	"use strict";

	var IconPressApp = function() {
		var base = this;

		this.panel = [];

		this.instanceId = false;
		this.context = false;

		base.init = function(instance_id, context) {
			var content = $(document);

			this.instanceId = instance_id;
			this.context = context;

			this.template_html = $("#tmpl-iconpress-panel").html();

			if (_.isUndefined(this.template_html)) {
				console.log("Panel's markup is not loaded.");
				return;
			}

			if (this.panel[this.instanceId] === void 0) {
				this.makePanel();
			}

			this.initEvents(content);
			this.addShortcodeToEditor();
		};

		base.makePanel = function() {
			// get html panel template
			var panel_template = _.template(this.template_html);
			// append to document
			$("body").append(panel_template({ instance_id: this.instanceId }));
			// make the instance panel
			this.panel[this.instanceId] = $("#ip-insertPanel-" + this.instanceId);
			// create its iframe
			$("<iframe>", {
				src: iconPressAppConfig.panel_url + "&context=" + this.context,
				frameborder: 0
			}).appendTo(base.panel[this.instanceId].children(".ip-insertPanel-inner"));
		};

		base.initEvents = function(content) {
			// open panel
			this.openIconPress();

			// close panel
			$(".ip-insertPanel-close, .ip-insertPanel-overlay", base.panel[this.instanceId]).on("click", function(
				event
			) {
				event.preventDefault();
				base.closeIconPress();
			});

			// reset SVG sprite contents on save/delete
			$(window).one("iconpress:save iconpress:delete", function(e) {
				base.resetSvgContents();
				// reset in iframes
				var iframes = [$("#customize-preview iframe"), $("#vc_inline-frame"), $("#elementor-preview-iframe")];
				$.each(iframes, function(index, el) {
					if ($(el).length !== 0) base.resetSvgContents($(el)[0].contentDocument);
				});
			});
		};

		base.openIconPress = function() {
			this.panel[this.instanceId].addClass("is-visible");
		};

		base.closeIconPress = function() {
			this.panel[this.instanceId].removeClass("is-visible");
		};

		/**
		 * WP EDITOR
		 */

		base.addShortcodeToEditor = function() {
			// Handle wp editor button
			$(window).one("iconpress:select:wpeditor", function(e) {
				e.preventDefault();
				// grab the settings
				var settings = e.detail;
				// check for instance
				if (base.instanceId == settings.instance_id) {
					// do the macarena
					base.sendToEditor(settings.shortcode, settings.instance_id);
					base.closeIconPress();
				}
			});
		};

		base.insertAtChars = function(_this, currentValue) {
			var obj = typeof _this[0].name !== "undefined" ? _this[0] : _this;

			if (obj.value.length && typeof obj.selectionStart !== "undefined") {
				obj.focus();
				return (
					obj.value.substring(0, obj.selectionStart) +
					currentValue +
					obj.value.substring(obj.selectionEnd, obj.value.length)
				);
			} else {
				obj.focus();
				return currentValue;
			}
		};

		base.sendToEditor = function(html, instance_id) {
			var tinymce_editor;
			var _parent = window.parent;

			if (typeof _parent.tinymce !== "undefined") {
				tinymce_editor = _parent.tinymce.get(instance_id);
			}

			if (tinymce_editor && !tinymce_editor.isHidden()) {
				tinymce_editor.execCommand("mceInsertContent", false, html);
			} else {
				var $editor = $("#" + instance_id, _parent.document);
				$editor.val(base.insertAtChars($editor, html)).trigger("change");
			}
		};

		base.resetSvgContents = function(target) {
			target = target || document;

			$.ajax({
				url: iconPressAppConfig.rest_url + "get_svg_sprite_content",
				method: "GET",
				cache: false,
				beforeSend: function(xhr) {
					xhr.setRequestHeader("X-WP-Nonce", iconPressAppConfig.nonce_rest);
				},
				success: function(resp) {
					// reset the SVG sprite with the newly added HTML
					// used in My Collection page
					var svgSprite = target.getElementById("iconpress_svg_sprite");

					if (svgSprite && resp.success && resp.data) {
						var tempDiv = target.createElement("div");
						tempDiv.innerHTML = resp.data;
						svgSprite.innerHTML = tempDiv.childNodes[0].innerHTML;
					} else {
						console.error("Couldn't reload svg sprite.");
					}
				}
			});
		};
	};

	$.IconPressApp = new IconPressApp();

	$(function() {
		// IconPress button in Text Widget
		if (iconPressAppConfig.insert_icon_button == 1) {
			$(document).on("tinymce-editor-init", function(event, editor) {
				var $btn = $(
					'<a href="#" class="button ip-wpEditor-insert" data-instance-id="' +
						editor.id +
						'"><svg class="iconpress-icon iconpress-icon-iconpress-logo " aria-hidden="true" style="font-size: 18px; margin-right: 5px;" role="img"> <use href="#iconpress-logo" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#iconpress-logo"></use> </svg><span>' +
						iconPressAppConfig.translations.INSERT_ICON +
						"</span></a>"
				);
				var $editorContainer = $(editor.container).closest(".wp-editor-wrap");
				$btn.appendTo($editorContainer.find(".wp-media-buttons"));
			});
		}

		$("body").on("click", ".ip-wpEditor-insert", function(event) {
			event.preventDefault();
			var instance_id = $(event.currentTarget).attr("data-instance-id");
			// initialize the panel
			$.IconPressApp.init(instance_id, "wpeditor");
		});

		//#! Internal admin notices - close button
		var adminNoticesCloseButton = $(".iconpress-notice-close-button");
		if (adminNoticesCloseButton && adminNoticesCloseButton.length > 0) {
			adminNoticesCloseButton.on("click", function(e) {
				window.location.href =
					document.URL +
					"&iconpress_delete_notices=1&" +
					iconPressAppConfig.nonce_name +
					"=" +
					iconPressAppConfig.nonce_value;
			});
		}

		$(".js-ip-passToggle").each(function(index, el) {
			var $el = $(el);
			$el.find('input[type="checkbox"]').on("change", function(e) {
				$el.find(".ip-passToggle-field").attr("type", $(e.currentTarget).prop("checked") ? "text" : "password");
			});
		});

		$("#ip-refreshCache").on("click", function(event) {
			event.preventDefault();

			if (typeof iconPressConfig.plugin_slug !== "undefined") {
				// remove cache
				localStorage.removeItem(iconPressConfig.plugin_slug + "_myCollection");

				var paneOptions = iconPressConfig.panes;
				if (paneOptions && paneOptions.length !== 0) {
					$.each(paneOptions, function(index, pane) {
						var type = pane.type;
						localStorage.removeItem(iconPressConfig.plugin_slug + "_filter_" + type);
						localStorage.removeItem(iconPressConfig.plugin_slug + "_collections_" + type + "_0");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_collections_" + type + "_1");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_collections_" + type + "_all");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_total_collections_" + type + "_0");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_total_collections_" + type + "_1");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_total_collections_" + type + "_all");
						localStorage.removeItem(iconPressConfig.plugin_slug + "_search_" + type);
					});
				}
				window.location.reload();
			}
		});
	});
})(jQuery);
