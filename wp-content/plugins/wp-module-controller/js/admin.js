(function ($, window, document, undefined) {

	window.kmc =  window.kmc || {};
	window.kmc.helpers = {
		render_options: function (options, current) {
			return _.map(options, function (option) {
				var value, label;
				if (typeof(option) === "object") {
					value = option[0];
					label = option[1];
				} else {
					value = label = option;
				}
				var html = '<option value="' + value + '"';
				if (value == current) html += ' selected="selected"';
				html += '>' + label + '</option>';
				return html;
			}).join();
		},

		render_tinymce_field: function (options) {
			/*
				Since 1.0.9
				Use this helper in templates to add tinymce fields. I.e.:

				<%= kmc.helpers.render_tinymce_field({
					name: 'col1',
					value: col1,
					dataUpdate: true,
					object: tinymceFields['col1']
				}) %>
			*/
			var name = options.name;
			var html = '<input type="hidden" data-id="' + name + '" class="tinymce-field"';
			if (options.dataUpdate) {
				html += ' data-update="' + name + '"';
			}
			html += ' value="' + options.value + '">';

			html += '<div class="tinymce-container">';
			if (options.object) {
				html += options.object.response;
			} else {
				html += '<div class="pal center mbl">Loading...</div>';
			}
			html += '</div>';

			return html;
		},

		get_cached_template: function (templateName) {
			window.kmc.templates = window.kmc.templates || {};
			if (!window.kmc.templates[templateName]) {
				window.kmc.templates[templateName] = _.template($(templateName).html());
			}

			return window.kmc.templates[templateName];
		}
	};

	$(function () {

		// ****************************** New Component Dialog ******************************
		var NewComponentDialog = Backbone.View.extend({
			template: _.template($("#kmc-new-dialog-template").html()),
			initialize: function (attributes) {
				var _this = this;
				this.callback = attributes.callback;
				this.on_add_existing_component_click = attributes.on_add_existing_component_click;

				this.render();
				if (attributes.sub_only) {
					this.$(".add-content").addClass("hidden");
					this.$("[data-sub-support]").removeClass("hidden");
				}
				$("body").append(this.$el);
				this.$(".col").css("min-height", this.$(".md-content").height() +"px");

				this.$modal = this.$(".md-modal");
				setTimeout(function () {
					_this.$modal.addClass("md-show");
				}, 100);
			},
			events: {
				"click .md-close": "on_close_click",
				"click .add-content": "on_add_content_click",
				"click .add-existing-module": "on_add_existing_component_click"
			},
			on_add_existing_component_click: function (e) {
				e.preventDefault();
				this.close_dialog();
				this.on_add_existing_component_click();
			},
			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},
			on_add_content_click: function (e) {
				e.preventDefault();
				var type = $(e.currentTarget).attr("data-type");

				this.callback(type);
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
				this.$el.html(this.template());
			}
		});

		window.kmc.NewComponentDialog = NewComponentDialog;



		// ****************************** Existing Component Dialog ******************************
		var ExistingComponentDialog = Backbone.View.extend({
			template: _.template($("#kmc-existing-dialog-template").html()),
			initialize: function (callback) {
				var _this = this;
				this.callback = callback;
				this.current_fetched_posts = [];

				this.render();
				$("body").append(this.$el);

				this.$modal = this.$(".md-modal");
				this.$select_type = this.$(".select-type");
				this.$select_module = this.$(".select-module");
				this.$select_module_container = this.$(".select-module-container");
				this.$resultList = this.$('.result-list');
				this.$noFound = this.$('.no-found');

				setTimeout(function () {
					_this.$modal.addClass("md-show");
				}, 100);
			},
			events: {
				"click .md-close": "on_close_click",
				"change .select-type": "on_type_change",
				"click .btn-add-module": "on_add_click"
			},
			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},
			on_type_change: function () {
				var _this = this;
				var type = this.$select_type.val();

				if (type == "-") {
					this.$select_module_container.addClass("hidden");
				} else {
					this.$select_module_container.removeClass("hidden");

					$.get(ajaxurl, {
						action: "fetch_modules",
						type: type
					}, function(response){
						if (response.status == 200){
							_this.$select_module.empty();
							_this.current_fetched_posts = response.data.posts;
							if (response.data.posts.length) {
								_.each(response.data.posts, function (post) {
									var title = post.post_title;
									if (post.language_details && post.language_details.language_code) {
										title += " (" + post.language_details.language_code + ")";
									}

									_this.$select_module.append(
										$("<option value='" + post.ID + "'></option>").text(title)
									);
								});

								_this.$resultList.removeClass('hidden');
								_this.$noFound.addClass('hidden');
							} else {
								_this.$resultList.addClass('hidden');
								_this.$noFound.removeClass('hidden');
							}
						} else {
							alert("An error occured..");
						}
					});
				}
			},
			on_add_click: function (e) {
				e.preventDefault();
				var _this = this;
				var post_id = this.$select_module.val();

				$.get(ajaxurl, {
					action: "fetch_module",
					id: post_id
				}, function(response){
					if (response.status == 200) {
						_this.callback(response.data.post);
						_this.close_dialog();
					} else {
						alert("An error occured..");
					}

				});
			},

			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			},

			render: function () {
				this.$el.html(this.template());
			}
		});


		// ****************************** Edit Section Dialog ******************************
		var EditSectionDialog = Backbone.View.extend({
			template: _.template($("#kmc-edit-section-dialog-template").html()),
			initialize: function (section, callback) {
				var _this = this;
				this.section = section;
				this.settings = this.section.get("settings") || {};
				this.callback = callback;
				this.tab_templates = [
					_.template($("#kmc-edit-section-dialog-tab-1-template").html()),
					_.template($("#kmc-edit-section-dialog-tab-2-template").html()),
					_.template($("#kmc-edit-section-dialog-tab-3-template").html())
				]

				this.$el.html(this.template());
				this.$content = this.$(".content");
				this.$(".tabs").tabs({
					activate: function(event, ui) {
						_this.render();
					}
				});
				$("body").append(this.$el);
				this.render();

				this.$modal = this.$(".md-modal");
				this.$modal.addClass("md-show");
			},
			events: {
				"click .md-close": "on_close_click",
				"click .btn-save-section-background": "on_save_background_click",
				"click .btn-save-section-style": "on_save_style_click",
				"click .btn-save-section-css": "on_save_css_click",
				"click .select-image": "on_select_image_click",
				"click .remove-image": "on_remove_image_click",
				"click .select-ogv-video": "on_select_ogv_video_click",
				"click .remove-ogv-video": "on_remove_ogv_video_click",
				"click .select-mp4-video": "on_select_mp4_video_click",
				"click .remove-mp4-video": "on_remove_mp4_video_click",
				"change .section-background-color": "on_bg_color_change",
				"click .clear-color-icon": "on_clear_color_click",
				"change .section-color-class": "on_color_class_change"
			},

			on_bg_color_change: function () {
				this.$(".clear-bg-color").toggleClass("hide", !this.$(".section-background-color").val());
			},
			on_clear_color_click: function (e) {
				var $icon = $(e.currentTarget);
				var $target = this.$("." + $icon.attr("data-target"));
				$target.val("").css("background-color", "inherit");
				$icon.addClass("hide");
			},
			on_color_class_change: function () {
				this.$(".color-container").toggleClass("hidden", this.$(".section-color-class").val() != "custom");
			},

			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},
			on_save_background_click: function () { 
				this.settings.name = this.$(".section-name").val();
				this.settings.background_color = this.$(".section-background-color").val();

				if (this.settings.background_image) {
					this.settings.background_image.size = this.$(".background-image-size").val();
				}
				this.callback(this.settings);
				this.close_dialog();
			},
			on_save_style_click: function () { 
				this.settings.color_class = this.$(".section-color-class").val();
				if (this.settings.color_class == "custom") {
					this.settings.color = this.$(".section-color").val();
				} else {
					this.settings.color = false;
				}
				this.settings.top_padding = this.$(".top-padding").val();
				this.settings.bottom_padding = this.$(".bottom-padding").val();
				this.settings.top_margin = this.$(".top-margin").val();
				this.settings.bottom_margin = this.$(".bottom-margin").val();
				this.callback(this.settings);
				this.close_dialog();
			},
			on_save_css_click: function () {
				this.settings.css = this.$(".section-css").val().replace("\n", " ");
				this.settings.extra_class = this.$(".section-extra-class").val();
				this.callback(this.settings);
				this.close_dialog();
			},
			on_select_image_click: function (e) {
				e.preventDefault();
				var _this = this;

				var image = wp.media({
					title: 'Select image',
					button: {
						text: 'Use this image'
					},
					multiple: false
				}).open().on('select', function(e){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					var img_data = uploaded_image.toJSON();
					var img = {};
					if (img_data["sizes"] && img_data["sizes"]["medium"]) {
						img.url = img_data["sizes"]["medium"].url;
					} else {
						img.url = img_data.url;
					}
					img.thumbnail = img_data["sizes"]["thumbnail"].url;
					img.full = img_data["sizes"]["full"].url;
					img.size = "cover";

					_this.settings["background_image"] = img;
					_this.render();
				});
			},

			on_select_ogv_video_click: function (e) {
				e.preventDefault();
				this.select_video('ogv');
			},

			on_select_mp4_video_click: function (e) {
				e.preventDefault();
				this.select_video('mp4');
			},

			select_video: function (type) {
				var _this = this;
				var video_media = wp.media({
					title: 'Select ' + type + ' video',
					button: {
						text: 'Use this video'
					},
					multiple: false
				}).open().on('select', function(e){
					var uploaded_video = video_media.state().get('selection').first();
					var video_data = uploaded_video.toJSON();
					var video = {
						filename: video_data.filename,
						filesizeInBytes: video_data.filesizeInBytes,
						fileLength: video_data.fileLength,
						mime: video_data.mime,
						url: video_data.url,
						subtype: video_data.subtype
					};
					_this.settings["background_video_" + type] = video;
					_this.render();
				});
			},

			on_remove_ogv_video_click: function (e) {
				e.preventDefault();
				this.settings["background_video_ogv"] = false;
				this.render();
			},

			on_remove_mp4_video_click: function (e) {
				e.preventDefault();
				this.settings["background_video_mp4"] = false;
				this.render();
			},

			on_remove_image_click: function (e) {
				e.preventDefault();
				this.settings["background_image"] = false;
				this.render();
			},
			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			},

			render: function () {
				var index = this.$(".tabs-head .ui-state-active").index();
				this.$("#tabs-" + (index + 1)).html(
					this.tab_templates[index]({
						settings: this.settings
					})
				);


				if (this.$(".jscolor").length) {
					this.$(".jscolor").each(function (index, elem) {
						var $elem = $(elem);
						if (!$elem.data("jscolor-init")) {
							$elem.data("jscolor-init", true);
							new jscolor(elem);
						}
					});
				}
			}
		});




		// ****************************** Edit Component Dialog ******************************
		var EditComponentDialog = Backbone.View.extend({
			template: _.template($("#kmc-edit-component-dialog-template").html()),
			initialize: function (component, callback) {
				var _this = this;
				this.component = component;

				this.settings = this.component.get("settings") || {};
				this.callback = callback;

				this.$el.html(
					this.template({
						settings: this.settings,
						fixedSettings: this.component.getModuleSettings().fixed || {}
					})
				);

				$("body").append(this.$el);

				this.$modal = this.$(".md-modal");
				this.$modal.addClass("md-show");
			},

			events: {
				"click .md-close": "on_close_click",
				"click .btn-save": "on_save_click"
			},

			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},
			on_save_click: function () {
				this.settings.width = this.$(".width").val();
				this.settings.top_padding = this.$(".top-padding").val();
				this.settings.bottom_padding = this.$(".bottom-padding").val();
				this.settings.top_margin = this.$(".top-margin").val();
				this.settings.bottom_margin = this.$(".bottom-margin").val();

				if (this.$(".animation").size()) {
					this.settings.animation = this.$(".animation").val();
					this.settings.animation_delay = this.$(".animation-delay").val();
				}

				this.callback(this.settings);
				this.close_dialog();
			},
			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			}
		});

		window.kmc.EditComponentDialog = EditComponentDialog;




		// ****************************** Import Content Dialog ******************************
		var ImportContentDialog = Backbone.View.extend({
			template: _.template($("#kmc-import-content-template").html()),
			initialize: function (callback) {
				var _this = this;
				this.callback = callback;
				this.current_fetched_posts = [];

				this.render();
				$("body").append(this.$el);

				this.$modal = this.$(".md-modal");
				this.$select_type = this.$(".select-type");
				this.$select_post = this.$(".select-post");
				this.$select_post_container = this.$(".select-post-container");

				setTimeout(function () {
					_this.$modal.addClass("md-show");
				}, 100);
			},
			events: {
				"click .md-close": "on_close_click",
				"change .select-type": "on_type_change",
				"click .btn-select-post": "on_select_click"
			},
			on_close_click: function (e) {
				e.preventDefault();
				this.close_dialog();
			},
			on_type_change: function () {
				var _this = this;
				var type = this.$select_type.val();

				if (type == "-") {
					this.$select_post_container.addClass("hidden");
				} else {
					this.$select_post_container.removeClass("hidden");

					$.get(ajaxurl, {
						action: "fetch_posts_for_post_type",
						type: type
					}, function(response){
						if (response.status == 200){
							_this.$select_post.empty();
							_this.current_fetched_posts = response.data.posts;
							_.each(response.data.posts, function (post) {
								var title = post.post_title;

								if (post.language_details) {
									title += " (" + post.language_details.language_code + ")";
								}

								_this.$select_post.append(
									$("<option value='" + post.ID + "'></option>").text(title)
								);
							});
						} else {
							alert("An error occured..");
						}
					});
				}
			},
			on_select_click: function (e) {
				e.preventDefault();
				var _this = this;
				var post_id = this.$select_post.val();

				$.get(ajaxurl, {
					action: "fetch_copy_content_from_post",
					id: post_id
				}, function(response){
					if (response.status == 200) {
						_this.callback(response.data.sections);
						_this.close_dialog();
					} else {
						alert("An error occured..");
					}

				});
			},

			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			},

			render: function () {
				this.$el.html(this.template());
			}
		});




		// ****************************** Section View ******************************
		var KmcSection = Backbone.Model.extend({
			initialize: function (attributes) {
				if (!this.get("settings")) {
					this.set("settings", {});
				}

				var _this = this;
				this.components = new kmc.KmcComponents();

				this.listenTo(this.components, "add", this.on_components_change);
				this.listenTo(this.components, "remove", this.on_components_change);

				this.view = new KmcSectionView({
					model: this
				});

				if (attributes && attributes.components) {
					_.each(attributes.components, function (component) {
						_this.add_component(component);
					});
				}

			},

			add_component: function (component, edit_mode) {

				if (component.post.post_type.substr(0, 6) === 'theme-') {
					// Add custom theme component
					var args = THEME_MODULES[component.post.post_type.substr(6)];
					var component = new KMC_MODULES_MODELS["theme"](component, args);
					this.components.add(component);

				} else {


					if (KMC_MODULES_MODELS[component.post.post_type]) {
						if (edit_mode === true) {
							$.extend(component, { edit: true });
						}
						var component = new KMC_MODULES_MODELS[component.post.post_type](component);
						this.components.add(component);
					} else {
						console.error("Could not find component for type: " + component.post.post_type);
					}

				}
			},

			add_component_type: function (type) {
				if (type.substr(0, 6) === 'theme-') {
					// Add custom theme component.
					// We need to pass along theme modules info
					var args = THEME_MODULES[type.substr(6)];
					var component = new KMC_MODULES_MODELS["theme"]({}, args);
					this.components.add(component);

				} else if (KMC_MODULES_MODELS[type]) {
					var component = new KMC_MODULES_MODELS[type]();
					this.components.add(component);

				} else {
					console.error("Could not find module: " + type);
				}
			},

			remove_component: function (component) {
				this.components.remove(component);
			},

			on_components_change: function () {
				this.trigger("change", this, this.components);
			},

			remove: function () {
				this.view.$el.remove();
				this.collection.remove(this);
			},

			to_json: function () {
				this.components.models = _.sortBy(this.components.models, 'position');
				return {
					components: _.map(this.components.models, function (component) {
						if (component.saved) {
							return component.get("post").ID;
						} else {
							return component.to_json();
						}

					}),
					settings: this.get("settings")
				}
			}
		});
		var KmcSectionView = Backbone.View.extend({
			template: _.template($("#kmc-module-section-template").html()),
			className: "kmc-section",
			initialize: function (attributes) {
				this.render();

				var _this = this;
				this.$section = this.$(".module-section");
				this.$container = this.$(".modules-container");
				this.$el.data("model", this.model);
				this.$container.data("model", this.model);


				this.listenTo(this.model.components, "add", this.on_component_added);
				this.listenTo(this.model, "change:settings", this.on_settings_changed);

				this.listenTo(this.model.components, "change", function () {
					this.model.trigger("change", this.model, this.model.components);
				});

				this.on_settings_changed();

				this.$container.sortable({
					handle: ".sort-handle",
					connectWith: ".modules-container",
					update: function(event, ui){
						_this.update_positions();

						if (ui.sender) {
							// Moved the component to another section
							var component = ui.item.data("model");
							ui.sender.data("model").remove_component(component)
							_this.model.components.add(component);
						}

						_this.model.trigger("change", _this.model, _this.model.components);
					}
				});
			},

			events: {
				"click .add-new-module": "on_add_new_component_click",
				"click .open-section-menu": "on_toggle_menu_click",
				"click .remove-section": "on_remove_section_click",
				"click .edit-section": "on_edit_section_click"
			},

			update_positions: function () {
				this.$(".module-box").each(function (index, element) {
					$(element).data("model").position = index;
				});
			},

			on_remove_section_click: function (e) {
				e.preventDefault();

				if (this.model.components.length) {
					if (confirm("Do you really want to delete this section?")) {
						var _this = this;
						this.$el.slideUp(function () {
							_this.model.remove();
						});
					}
				} else {
					var _this = this;
					this.$el.slideUp(function () {
						_this.model.remove();
					});
				}
			},

			on_edit_section_click: function (e) {
				e.preventDefault();
				var _this = this;
				new EditSectionDialog(this.model, function (settings) {
					_this.model.set("settings", settings);
					_this.on_settings_changed();
					_this.model.trigger("change", _this.model);
				});
			},

			on_toggle_menu_click: function (e) {
				e.preventDefault();
				this.$section.toggleClass("add-visible");
				var open = this.$section.hasClass("add-visible");
				$(e.currentTarget).closest(".section-menu").toggleClass("open", open);
			},

			on_add_new_component_click: function (e) {
				e.preventDefault();
				var _this = this;

				new NewComponentDialog({
					sub_only: false,
					on_add_existing_component_click: this.on_add_existing_component_click.bind(this),
					callback: function (type) {
						_this.model.add_component_type(type);
					}
				});
			},

			on_add_existing_component_click: function () {
				var _this = this;

				new ExistingComponentDialog(function (post) {
					_this.model.add_component(post);
				});
			},

			on_component_added: function (component) {
				this.$container.append(component.view.$el);
			},

			on_settings_changed: function (section) {
				var settings = this.model.get("settings");
				this.$section.css("background-color", settings.background_color || "");
				this.$section.removeClass("dark light custom");
				this.$section.addClass(settings.color_class);
				if (settings.color_class == "custom") {
					this.$section.css("color", settings.color);
				}
				if (settings.background_image) {
					this.$section.css("background-image", "url('" + settings.background_image.full + "')");
					if (settings.background_image.size == "repeat") {
						this.$section.css("background-repeat", "repeat");
						this.$section.css("background-size", "inherit");
					} else {
						this.$section.css("background-repeat", "no-repeat");
						if (settings.background_image.size == "cover") {
							this.$section.css("background-size", "cover");
						} else {
							this.$section.css("background-size", "contain");
						}
					}
				} else {
					this.$section.css("background-image", "none");
					this.$section.css("background-repeat", "no-repeat");
					this.$section.css("background-size", "inherit");
				}

			},

			render: function () {
				this.$el.html(
					this.template(
						this.model.attributes
					)
				);
			}
		});
		var KmcSections = Backbone.Collection.extend({
			model: KmcSection,
			to_json: function () {
				this.models = _.sortBy(this.models, 'position');
				var sections = _.map(this.models, function (section) {
					return section.to_json();
				});
				return sections;
			}
		});


		// ****************************** Controller View ******************************
		var ModuleControllerView = Backbone.View.extend({
			el: "#kmc-meta-box",
			initialize: function (attributes) {
				var _this = this;
				this.$container = this.$(".section-container");
				this.sections = new KmcSections();
				this.$field = this.$("#kmc_page_components");

				this.listenTo(this.sections, "add", this.on_section_added);

				// Add existing sections
				_.each(attributes.sections, function (section) {
					_this.sections.add(new KmcSection(section));
				});

				// Made it possible to change section order
				this.$container.sortable({
					handle: ".add-visible .sort-handle",
					update: function(event, ui){
						_this.update_positions();
					}
				});

				// Update the meta data when the form is submitted
				$("form#post").on("submit", function (e) {
					_this.update_field();
				});

				if (window.kmc.logEnabled) {
					console.log("INIT");
					console.log("KMC_MODULES_MODELS:", KMC_MODULES_MODELS);
					console.log("THEME_MODULES:", THEME_MODULES);
					console.log("Sections:", this.sections);
				}

				window.kmc.debug_json = _.bind(this.debug_json, this);
			},

			reset_spacing: function () {
				var _this = this;
				_.each(this.sections.models, function (section) {
					var settings = section.get("settings");
					settings.top_padding = "";
					settings.bottom_padding = "";
					settings.top_margin = "";
					settings.bottom_margin = "";
					section.set("settings", settings);
					section.trigger("change", section);
					section.view.on_settings_changed();

					_.each(section.components.models, function (component) {
						var component_settings = component.get("settings");
						if (component_settings) {
							component_settings.top_padding = "";
							component_settings.bottom_padding = "";
							component_settings.top_margin = "";
							component_settings.bottom_margin = "";
							component.set("settings", component_settings);
							component.set("changed", true);
						}
					});
				});
			},

			events: {
				"click .add-new-section": "on_add_new_section_click",
				"click .btn-import": "on_import_click"
			},

			update_positions: function () {
				this.$(".kmc-section").each(function (index, element) {
					$(element).data("model").position = index;
				});
			},

			on_add_new_section_click: function () {
				var section = new KmcSection();
				section.view.$section.toggleClass("add-visible");
				section.view.$(".section-menu").toggleClass("open", true);
				this.sections.add(section);
			},

			on_import_click: function () {
				var _this = this;
				new ImportContentDialog(function (sections) {
					_.each(sections, function (section) {
						// Strip IDs to create new components when saved
						_.each(section.components, function (component) {
							// TODO: Move this logic to components
							if (component.type != "tabs" && component.type != "imageslider" && component.type != "alertbar") {
								delete component.id;
								delete component.post.ID;
								component.copied = true;
							}

							if (component.type == "box-area") {
								_.each(component.boxes, function (box) {
									delete box.post.ID;
								});
							} else if (component.type == "tabs") {
								if (!confirm('Vill du importera tabbarna "' +  component.post.post_title + '" som referens?')) {
									delete component.id;
									delete component.post.ID;
									component.copied = true;

									_.each(component.tabs, function (tab) {
										_.each(tab.components, function (tab_component) {
											delete tab_component.id;
											delete tab_component.post.ID;
										});
									});
								}

							}
						});
						_this.sections.add(new KmcSection(section));
					});
				});
			},

			on_section_added: function (section) {
				this.$container.append(section.view.$el);
			},

			update_field: function () {
				var data = this.sections.to_json();
				var json = JSON.stringify(data);
				this.$field.val(json);
			},

			debug_json: function () {
				var data = this.sections.to_json();
				var json = JSON.stringify(data);
				console.log("DATA:", data);
				console.log("JSON: ", json);
			}
		});

		window.temptemp = new ModuleControllerView({
			modules: kmc_modules_classes,
			sections: kmc_sections
		});
	});

}(jQuery, window, document));