(function ($, window, document, undefined) {

	$(function () {

		var KmcComponent = Backbone.Model.extend({
			edit_when_added: true,
			label: "-",
			show_settings: true,
			defaults: {
				edit: false
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
				if (this.get("copied")) {
					this.set("edit", false);
					this.set("saved", false);
					this.set("changed", true);
				} else if (this.get("post") && this.get("post").ID) {
					this.set("saved", true);
					this.set("changed", false);
				} else {
					this.set("edit", this.edit_when_added);
					this.set("saved", false);
					this.set("changed", true);
					if (this.extra_defaults) {
						this.set($.extend({}, this.extra_defaults));
					}
				}

				// Make sure all extra defaults attributes is set.
				// This will prevent existing components to break
				// when new attributes are added.
				if (this.extra_defaults) {
					_.each(this.extra_defaults, function (val, attr) {
						if (!_this.attributes[attr]) {
							_this.set(attr, val);
						}
					});
				}
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
				this.trigger("change");
			},
			set_content: function (content) {
				var post = this.get("post");
				post.post_content = content;
				this.set("changed", true);
				this.trigger("change");
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
					this.$el.toggleClass("edit", this.model.get("edit"));

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
			},
			events: {
				"click .edit": "on_edit_click",
				"click .remove": "on_remove_click",
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

			on_edit_settings_click: function (e) {
				e.preventDefault();
				var _this = this;
				new kmc.EditComponentDialog(this.model, function (settings) {
					if (settings.break_free) {
						console.log("BREAK FREE", _this.model, settings);
						delete settings.break_free;
						delete _this.model.attributes.id;
						delete _this.model.attributes.post.ID;
						_this.model.set("saved", false);

						if (_this.model.attributes.type == "box-area") {
							_.each(_this.model.attributes.boxes.models, function (box) {
								delete box.attributes.id;
								delete box.attributes.post.ID;
							});
						} else if (_this.model.attributes.type == "tabs") {
							delete _this.model.attributes.id;
							delete _this.model.attributes.post.ID;

							_.each(_this.model.get("tabs").models, function (tab) {
								//tab.set("saved", false);
								console.log("Tab", tab);
								_.each(tab.components.models, function (tab_component) {
									console.log("TAb component", tab_component);
									delete tab_component.attributes.id;
									delete tab_component.attributes.post.ID;
									tab_component.set("saved", false);
								});
								console.log("CHECK", tab.to_json());
							});
						}
					}

					console.log("SETTINGS CLOSED", _this.model.attributes);

					_this.model.set("settings", settings);
					_this.model.set("changed", true);

				});
			},

			render_base: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);

				this.$header = this.$(".header:first");
				if (this.$header.length) {
					this.render_header();
				}
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
				//this.model.trigger("change");
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