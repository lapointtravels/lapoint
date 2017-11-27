(function ($, window, document, undefined) {

	$(function () {

		var ContentComponent = kmc.KmcComponent.extend({
			type: "kmc_content",
			label: "Content",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new ContentComponentView({
					model: this
				});
			},
			setup_preview_view: function () {
				this.preview_view = new ContentComponentPreviewView({
					model: this
				});
			}
		});

		var ContentComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-content-component-template").html()),
			content_template: _.template($("#kmc-content-component-inner-template").html()),
			auto_set_title: ".post-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.tinymce_loaded = false;

				// Render box structure
				this.render_base();

				this.$content_container = this.$(".content-container");
				this.$edit_container = this.$(".edit-container");
				this.$tinymce_container = this.$(".tinymce-container");

				this.listenTo(this.model, "change:title", this.render);

				this.render();
			},

			on_content_change: function () {
				this.model.set_content(this.tinymce_editor.getContent());
			},

			refresh: function () {
				// Called when the elem is readded to the dom, by another container module i.e. tabs
				if (this.tinymce_editor) {
					this.tinymce_editor.destroy();
					tinymce.init(this.tiny_settings);
					this.tinymce_editor = tinymce.get('txt-editor-' + this.editor_id);
				}
			},

			setup_tinymce: function (response, editor_id) {
				var _this = this;
				this.editor_id = editor_id;
				this.$tinymce_container.html(response);
				quicktags({id : 'txt-editor-' + editor_id});

	            // Mirror the settings from the content tinymce instance, but replace the ID
				this.tiny_settings = tinymce.extend({}, tinyMCEPreInit.mceInit["content"], {
					selector: "#txt-editor-" + editor_id,
					setup : function (ed) {
						ed.onChange.add(function (ed, l) {
							_this.on_content_change();
						});
					}
				});
				tinymce.init(this.tiny_settings);

				this.tinymce_loaded = true;
				this.tinymce_editor = tinymce.get('txt-editor-' + this.editor_id);

			},
			render: function () {
				var _this = this;
				this.$el.toggleClass("edit", this.model.get("edit"));

				if (this.model.get("edit")) {

					this.$content_container.html(
						this.content_template(this.model.attributes)
					);

					if (!this.tinymce_loaded) {

						if (this.model.get("saved")) {
							$.post(ajaxurl, {
								action: "component_action",
								post_id: this.model.get("id"),
								component_action: "get_tinymce_editor"
							}, function (response) {
								_this.setup_tinymce(response, _this.model.get("id"));
							});
						} else {

							var editor_id = Math.round(Math.random() * 100000);

							$.post(ajaxurl, {
								action: "module_action",
								module: "kmc_content",
								module_action: "get_tinymce_editor",
								editor_id: editor_id
							}, function (response) {
								_this.setup_tinymce(response, editor_id);
							});
						}

					}
				} else {

					this.$content_container.html(
						this.content_template(this.model.attributes)
					);
				}
			}
		});


		var ContentComponentPreviewView = Backbone.View.extend({
			template: _.template($("#kmc-content-preview-template").html()),
			initialize: function () {
				this.listenTo(this.model, "change", this.render);
				this.render();
			},
			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		window.KMC_MODULES_MODELS["kmc_content"] = ContentComponent;

	});

}(jQuery, window, document));