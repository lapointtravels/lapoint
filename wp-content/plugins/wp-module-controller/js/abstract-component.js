(function ($, window, document, undefined) {

	$(function () {

		// ****************************** Edit Component Dialog ******************************
		var ComponentInfoDialog = Backbone.View.extend({
			template: _.template($("#kmc-component-info-dialog-template").html()),
			inner_template: _.template($("#kmc-component-info-dialog-inner-template").html()),
			initialize: function (component, callback) {
				var _this = this;
				this.component = component;
				this.callback = callback;

				this.$el.html(
					this.template({
						component: this.component
					})
				);
				this.$container = this.$(".md-body");
				this.render();

				$("body").append(this.$el);
				this.$modal = this.$(".md-modal");
				this.$modal.addClass("md-show");

				if (this.component.get("shared")) {
					this.get_component_info();
				}
			},
			events: {
				"click .md-close": "on_close_click",
				"click .break-free": "on_break_free_click",
				"click .btn-share": "on_share_click"
			},

			on_share_click: function (e) {
				e.preventDefault();
				e.stopPropagation();
				this.component.set({
					shared: true,
					changed: true
				});
				this.render();
				this.get_component_info();
			},

			get_component_info: function () {
				var _this = this;

				$.get(ajaxurl, {
					action: "get_component_info",
					component_id: this.component.get("id")
				}, function (response) {
					if (response.status == 200 && response.data.count > 1) {
						var info = "This component is used on " + response.data.count + " " + (response.data.count == 1 ? "post:" : "posts:");
						_this.$(".component-info .info").text(info)
						_.each(response.data.pages, function (page) {
							_this.$(".component-info .info").append(
								$("<a href='" + page.link + "'></a>").text(page.title).css("display", "block")
							);
						});

						_this.$(".break-free").removeClass("hidden");
						_this.$(".component-info").removeClass("hidden");
					} else if (response.status == 200 && (response.data.count === 1 || response.data.count === "1")) {
						var info = "There is only one instance of this component available, and it's the one on this page.";
						_this.$(".component-info .info").text(info);
						_this.$(".component-info").removeClass("hidden");
					} else if (response.status == 200 && response.data.count === 0) {
						var info = "This component has not been saved yet, and is only available on the current page.";
						_this.$(".component-info .info").text(info);
						_this.$(".component-info").removeClass("hidden");

					} else {
						console.error("An error occured while trying to fetch component info", response)
					}
				});
			},

			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},

			on_break_free_click: function () {
				this.callback({
					break_free: true
				});
				this.close_dialog();
			},

			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			},

			render: function () {
				this.$container.html(
					this.inner_template({
						component: this.component
					})
				);
			}
		});


		// ****************************** Component ******************************
		var KmcComponent = Backbone.Model.extend({
			edit_when_added: true,
			label: "-",
			show_settings: true,
			defaults: {
				edit: false,
				shared: false
			},

			initialize: function (attributes) {
				var _this = this;

				if (!this.get("post")) {
					this.set("post", {
						post_title: "",
						post_content: ""
					});
				}

				this.set("type", this.type);
				var _moduleSettings = this.getModuleSettings();

				if (this.get("copied")) {
					this.set({
						edit: false,
						saved: false,
						changed: true
					});
				} else if (this.get("post") && this.get("post").ID) {
					// Existing component
					this.set({
						saved: true,
						changed: false
					});
				} else {
					// New component
					this.set({
						edit: this.edit_when_added,
						saved: false,
						changed: true
					});
					if (this.extra_defaults) {
						this.set($.extend({}, this.extra_defaults));
					}

					var settings = {};
					_.each(_moduleSettings.defaults, function (val, key) {
						settings[key] = val;
					});
					_.each(_moduleSettings.fixed, function (val, key) {
						settings[key] = val;
					});
					this.set('settings', settings);
				}

				// Make sure all extra defaults attributes is set.
				// This will prevent existing components to break
				// when new attributes are added.
				if (this.extra_defaults) {
					_.each(this.extra_defaults, function (val, attr) {
						if (!_this.attributes[attr]) {
							_this.set(attr, val);
							_this.set('changed', true);
						}
					});
				}
			},

			getModuleSettings: function () {
				// Get fixed module settings. These settings can't be set by user in the dialog.
				// They should be defined on the module object in a "fixed_settings" attribute.
				if (!this._moduleSettings) {
					var type = this.get('type');
					var moduleSettings = {};

					if (type.substr(0, 6) === 'theme-') {
						if (THEME_MODULES[type.substr(6)]) {
							moduleSettings = THEME_MODULES[type.substr(6)].module_settings || {};
						}
					} else {
						if (KMC_MODULES_MODELS[type]) {
							moduleSettings = KMC_MODULES_MODELS[type].module_settings || {};
						}
					}
					this._moduleSettings = moduleSettings;
				}

				return this._moduleSettings;
			},

			set_post_data: function (post_data) {
				var post = this.get("post");
				post = $.extend(post, post_data);
				this.set("changed", true, { silent: true });
			},

			set_title: function (title) {
				var post = this.get("post");
				post.post_title = title;
				this.set("changed", true);
				this.trigger("change", this);
			},
			set_content: function (content) {
				var post = this.get("post");
				post.post_content = content;
				this.set("changed", true);
				this.trigger("change", this);
			},

			remove: function () {
				this.view.$el.remove();
				this.collection.remove(this);
			},

			to_json: function () {
				return this.toJSON();
			}
		});

		var KmcComponentView = Backbone.View.extend({
			header_template: _.template($("#kmc-component-head-template").html()),
			className: "module-box",
			base_only: false,
			auto_set_title: false,
			auto_set_content: false,
			initialize: function () {
				this.$el.addClass(this.model.get("type"));
				this.tinymceFields = [];
				this.tinymceFieldsMap = {};

				if (this.auto_set_title) {
					var event_object = {};
					event_object["change " + this.auto_set_title] = "on_title_change";

					this.events = $.extend(this.events, event_object);
				}

				if (this.auto_set_content) {
					var event_object = {};
					event_object["change " + this.auto_set_content] = "on_content_change";

					this.events = $.extend(this.events, event_object);
				}
				if (this.extra_events) {
					this.events = $.extend(this.events, this.extra_events);
				}

				this.$el.data("model", this.model);
				if (this.model.get("saved")) {
					this.$el.attr("data-post-id", this.model.get("post").ID);
				}

				this.$el.toggleClass("edit", this.model.get("edit"));

				// Listen for model changes
				// this.listenTo(this.model, "change:edit", this.render);
				var _this = this;
				this.listenTo(this.model, "change:edit", function () {
					_this.$el.toggleClass("edit", _this.model.get("edit"));

					if (this.base_only) {
						_this.render_base();
					} else {
						_this.render();
						_this.$header = this.$(".header:first");
						if (_this.$header.length) {
							_this.render_header();
						}
					}
				});


				this.$el.toggleClass('shared', !!this.model.get('shared'));
				this.listenTo(this.model, "change:shared", function () {
					_this.$el.toggleClass('shared', _this.model.get('shared'));
				});

				this.delegateEvents();
			},
			events: {
				"dblclick .kmc-preview": "on_edit_click",
				"click .edit": "on_edit_click",
				"click .remove": "on_remove_click",
				"click .component-info": "on_info_click",
				"click .btn-return": "stop_editing",
				"click .edit-settings": "on_edit_settings_click",
				"change [data-update]": "on_field_changed"
			},


			on_title_change: function (e) {
				this.model.set_title(this.$(this.auto_set_title).val());

				this.$header = this.$(".header:first");
				if (this.$header.length) {
					this.render_header();
				}
			},
			on_content_change: function (e) {
				this.model.set_content(this.$(this.auto_set_content).val());
			},

			on_field_changed: function (e) {
				var $field = $(e.currentTarget);
				this.model.set($field.attr("data-update"), $field.val());
				this.model.set("changed", true);
			},

			on_info_click: function (e) {
				e.preventDefault();
				var _this = this;
				new ComponentInfoDialog(this.model, function (data) {
					if (data.break_free) {
						delete _this.model.attributes.id;
						delete _this.model.attributes.post.ID;
						_this.model.set({
							saved: false,
							shared: false
						});

						if (_this.model.attributes.type == "box-area") {
							_.each(_this.model.attributes.boxes.models, function (box) {
								delete box.attributes.id;
								delete box.attributes.post.ID;
							});
						} else if (_this.model.attributes.type == "tabs") {
							delete _this.model.attributes.id;
							delete _this.model.attributes.post.ID;

							_.each(_this.model.get("tabs").models, function (tab) {
								_.each(tab.components.models, function (tab_component) {
									delete tab_component.attributes.id;
									delete tab_component.attributes.post.ID;
									tab_component.set("saved", false);
								});
							});
						}
					}
				});
			},

			on_edit_settings_click: function (e) {
				e.preventDefault();
				e.stopPropagation();
				var _this = this;
				new kmc.EditComponentDialog(this.model, function (settings) {
					_this.model.set({
						settings: settings,
						changed: true
					});
				});
			},

			render_base: function (extra) {
				this.$el.html(
					this.template(Object.assign({}, {tinymceFields: this.tinymceFields}, this.model.attributes, extra || {}))
				);

				this.$header = this.$(".header:first");
				if (this.$header.length) {
					this.render_header();
				}

				this.checkTinymceFields();
			},

			render_header: function () {
				this.$header.html(
					this.header_template(
						$.extend({}, this.model.attributes, {
							label: this.model.label,
							show_settings: this.model.show_settings
						})
					)
				);
			},

			stop_editing: function (e) {
				e.preventDefault();
				this.model.set("edit", false);
				//this.model.trigger("change", this);
			},

			// ****************************** TinyMCE ******************************
			_load_tinymce_editor: function (tinyObj) {
				var _this = this;
				$.post(ajaxurl, {
					action: "get_empty_tinymce_editor",
					id: tinyObj.id
				}, function (response) {
					tinyObj.response = response;
					tinyObj.isLoaded = true;
					_this.render_base();
				});
			},

			_on_tiny_content_change: function (tinyObj) {
				var $ref = $('#' + tinyObj.refField.attr('id'));
				var content = tinyObj.editor.getContent();
				$ref.val(content);

				if ($ref.attr('data-update')) {
					this.model.set($ref.attr('data-update'), content);
					this.model.set('changed', true);
				}
			},

			checkTinymceFields: function () {
				var _this = this;

				this.$('.tinymce-field').each(function (index, el) {
					var $el = $(el);

					var elId = $el.attr("data-id") || $el.attr("data-update") || false;

					if (elId) {
						if (!_this.tinymceFields[elId]) {
							_this.tinymceFields[elId] = {
								id: 'auto-tiny-' + Math.round(Math.random() * 100000),
								refField: $el,
								isLoaded: false,
								response: false,
								settings: false,
								editor: false
							}
						}

						var tinyObj = _this.tinymceFields[elId];
						if (!$el.attr('id')) {
							$el.attr('id', 'ref-' + tinyObj.id);
						}

						if (!tinyObj.isLoaded) {
							_this._load_tinymce_editor(tinyObj);
						} else {
							if (tinyObj.settings) {
								// The editor has already been loaded. Remove it
								// and readd it to get all bindings set up correctly.
								tinyObj.editor = tinymce.get(tinyObj.id);
								if (tinyObj.editor) {
									tinyObj.editor.remove();
								}
							}

							quicktags({id : tinyObj.id});

				            // Mirror the settings from the content tinymce instance, but replace the ID
							tinyObj.settings = tinymce.extend({}, tinyMCEPreInit.mceInit["content"], {
								selector: '#' + tinyObj.id,
								setup : function (ed) {
									ed.onChange.add(function (ed, l) {
										_this._on_tiny_content_change(tinyObj);
									});
								}
							});
							tinymce.init(tinyObj.settings);
							tinyObj.editor = tinymce.get(tinyObj.id);

							tinyObj.editor.setContent($('#' + tinyObj.refField.attr('id')).val());
						}
					}

				});
			},

			on_edit_click: function (e) {
				e.preventDefault();
				this.model.set("edit", true);
			},
			on_remove_click: function (e) {
				e.preventDefault();
				this.model.remove();
			}
		});


		var KmcComponents = Backbone.Collection.extend({
			model: KmcComponent
		});


		window.kmc =  window.kmc || {};
		window.kmc.KmcComponent = KmcComponent;
		window.kmc.KmcComponentView = KmcComponentView;
		window.kmc.KmcComponents = KmcComponents;
	});

}(jQuery, window, document));