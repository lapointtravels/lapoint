(function ($, window, document, undefined) {

	$(function () {

		var ThemeComponent = kmc.KmcComponent.extend({
			type: 'theme-section',
			label: 'Theme section',
			initialize: function (attributes, data) {
				var _this = this;
				this.extra_defaults = {};
				this.data = data;

				_.each(data.fields, function (field) {
					_this.extra_defaults[field.key] = field.default || '';
				});

				this.label = data.title,
				this.type = 'theme-' + data.key,
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new ThemeComponentView({
					model: this
				});
			},

			setup_preview_view: function () {
				this.preview_view = new ThemeComponentPreviewView({
					template: _.template($('#' + this.data.admin_preview_template_name).html()),
					model: this
				});
			}
		});

		var ThemeComponentPreviewView = Backbone.View.extend({
			initialize: function (attributes) {
				this.template = attributes.template;
				this.listenTo(this.model, "change", this.render);
				this.render();
			},
			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		var ThemeComponentView = kmc.KmcComponentView.extend({
			template: _.template($('#kmc-theme-component-template').html()),
			image_row_template: _.template($('#kmc-tc-image-row-template').html()),

			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.adminTemplate = false;
				if (this.model.data.admin_template_name) {
					this.adminTemplate = _.template($('#' + this.model.data.admin_template_name).html());
				}

				var _this = this;
				// this.imageFields = {};
				this.imageFieldsSizes = {};
				_.each(this.model.data.fields, function (field) {
					if (field.type === 'image') {
						// _this.imageFields[field.key] = _this.model.get(field.key);
						_this.imageFieldsSizes[field.key] = field.sizes;
					}

					if (field.type === 'select') {
						// Set default select values if not set
						if (typeof(field.options[_this.model.get(field.key)]) === 'undefined') {
							_this.model.set(field.key, _.keys(field.options)[0])
						}
					}
				});

				this.render();
			},

			extra_events: {
				'click .select-image': 'on_select_image_click',
				'click .remove-image': 'on_remove_image_click'
			},

			render_image_row: function ($el) {
				var key = $el.attr('data-key');
				// var image = this.imageFields[key];
				var image = this.model.get(key);

				$el.html(
					this.image_row_template({
						image: image
					})
				);
				if (image) {
					$el.addClass('image-row');
				} else {
					$el.removeClass('image-row');
				}
			},

			on_select_image_click: function (e) {
				e.preventDefault();
				var _this = this;
				var $el = $(e.currentTarget);
				var $row = $el.closest('.image-field');
				var key = $row.attr('data-key');

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
					img.thumbnail = img_data['sizes']['thumbnail'].url;
					img.url = img_data.url;
					img.sizes = {};

					// Save only sizes specified in the box data
					// TODO: Atm custom sizes are not set unless the ACF plugin is included..
					_.each(_this.imageFieldsSizes[key], function (size) {
						img.sizes[size] = img_data['sizes'][size];
					});

					_this.model.set(key, img);
					_this.model.set("changed", true);

					_this.render_image_row($row);
				});
			},

			on_remove_image_click: function (e) {
				e.preventDefault();
				var $el = $(e.currentTarget);
				var $row = $el.closest('.image-field');
				var key = $row.attr('data-key');

				// this.imageFields[key] = false;
				this.model.set(key, false);
				this.render_image_row($row);
			},

			render: function () {
				var _this = this;
				var admin_presentation;
				if (this.adminTemplate) {
					admin_presentation = this.adminTemplate({
						attributes: this.model.attributes
					});
				} else {
					admin_presentation = this.model.data.admin_presentation;
					_.each(this.model.data.fields, function (field) {
						admin_presentation = admin_presentation.replace('[' + field.key + ']', _this.model.get(field.key));
					});
				}


				this.render_base({
					fields: this.model.data.fields,
					admin_presentation: admin_presentation,
					all: this.model.attributes
				});

				// Render image fields
				if (this.$('.image-field').size()) {
					this.$('.image-field').each(function (index, elem) {
						_this.render_image_row($(elem));
					});
				}
			}
		});

		window.KMC_MODULES_MODELS["theme"] = ThemeComponent;

	});

}(jQuery, window, document));