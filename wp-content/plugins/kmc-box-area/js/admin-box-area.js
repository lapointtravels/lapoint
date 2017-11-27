(function ($, window, document, undefined) {

	$(function () {


		// ****************************** Text Box Dialog ******************************
		var TextBoxDialog = Backbone.View.extend({
			template: _.template($("#kmc-text-box-dialog-template").html()),
			initialize: function (attributes) {
				var _this = this;
				this.box = attributes.box || false;
				this.callback = attributes.callback;

				this.$el.html(this.template({
					box: this.box
				}));
				this.$content = this.$(".content");
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
				var data = {
					post: {
						post_type: "area-text-box",
						post_title: this.$(".box-title").val(),
						post_content: this.$(".box-content").val()
					},
					button_text: this.$(".box-button-text").val(),
					button_link: this.$(".box-button-link").val()
				};

				if (this.box) {
					data.post.ID = this.box.get("post").ID;
				}

				this.callback(data);
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


		// ****************************** Preview Box Dialog ******************************
		var PreviewBoxDialog = Backbone.View.extend({
			template: _.template($("#kmc-preview-box-dialog-template").html()),
			image_row_template: _.template($("#kmc-box-image-row-template").html()),
			initialize: function (attributes) {
				var _this = this;
				this.box = attributes.box || false;
				this.background_image = this.box ? this.box.get("background_image") : false;
				this.callback = attributes.callback;

				this.$el.html(this.template({
					box: this.box
				}));
				this.render_image_row();
				this.$content = this.$(".content");
				$("body").append(this.$el);

				this.$modal = this.$(".md-modal");
				this.$modal.addClass("md-show");
			},
			events: {
				"click .md-close": "on_close_click",
				"click .btn-save": "on_save_click",
				"click .select-image": "on_select_image_click",
				"click .remove-image": "on_remove_image_click"
			},
			render_image_row: function () {
				this.$(".image-select-row").html(
					this.image_row_template({
						image: this.background_image
					})
				);
				if (this.background_image) {
					this.$(".image-select-row").addClass("image-row");
				} else {
					this.$(".image-select-row").removeClass("image-row");
				}
			},
			on_close_click: function (e) {
				e.preventDefault();
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
					/*if (img_data["sizes"] && img_data["sizes"]["medium"]) {
						img.url = img_data["sizes"]["medium"].url;
					} else {
						img.url = img_data.url;
					}*/
					img.thumbnail = img_data["sizes"]["thumbnail"].url;
					//img.data = img_data;
					img.sizes = {};
					img.sizes["box-md"] = img_data["sizes"]["box-md"];
					img.sizes["box-sm"] = img_data["sizes"]["box-sm"];

					_this.background_image = img;
					_this.render_image_row();
				});
			},
			on_remove_image_click: function (e) {
				e.preventDefault();
				this.background_image = false;
				this.render_image_row();
			},
			on_save_click: function () {
				var data = {
					post: {
						post_type: "area-preview-box",
						post_title: this.$(".box-title").val(),
						post_content: this.$(".box-content").val()
					},
					background_image: this.background_image,
					cols_md: this.$(".box-cols-md").val(),
					rows_md: this.$(".box-rows-md").val(),
					cols_sm: this.$(".box-cols-sm").val(),
					rows_sm: this.$(".box-rows-sm").val(),
					button_text: this.$(".box-button-text").val(),
					button_link: this.$(".box-button-link").val()
				};

				if (this.box) {
					data.post.ID = this.box.get("post").ID;
				}

				this.callback(data);
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



		// ****************************** Boxes ******************************
		var AreaBox = Backbone.Model.extend({
			initialize: function (attributes) {
				this.set("type", this.type);
				this.set("label", this.label);
				this.row_view = new BoxRowView({
					model: this
				});
			},
			remove: function () {
				console.log("Remove");
				this.row_view.remove();
				this.trigger("change");
				this.collection.remove(this);
			}
		});
		var AreaBoxes = Backbone.Collection.extend({
			model: AreaBox,
			/*sort_key: "position",
		    comparator: function(item) {
		        return item.get(this.sort_key);
		    },*/

			/*to_json: function () {
				this.models = _.sortBy(this.models, 'position');
				return _.map(this.models, function (box) {
					return box.get("post").ID;
				});
			}*/
		});
		var BoxRowView = Backbone.View.extend({
			tagName: "tr",
			template: _.template($("#kmc-box-area-box-row-template").html()),
			initialize: function () {
				this.render();
				this.$el.data("model", this.model);
			},
			events: {
				"click .remove-link": "on_remove_click",
				"click .edit-link": "on_edit_click"
			},
			on_remove_click: function (e) {
				e.preventDefault();
				this.model.remove();
			},
			on_edit_click: function (e) {
				e.preventDefault();
				var _this = this;

				var Dialog = false;
				switch (this.model.get("post").post_type) {
					case "area-text-box":
						Dialog = TextBoxDialog;
						break;
					case "area-preview-box":
						Dialog = PreviewBoxDialog;
						break;
				}

				if (!Dialog){
					console.log("No dialog", this.model.get("post"));
				}

				if (Dialog) {
					new Dialog({
						box: this.model,
						callback: function (box_data) {
							_this.model.set(box_data);
							_this.model.set("changed", true);
							_this.model.trigger("change");
							_this.render();
						}
					});
				}
			},
			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		// ****************************** Text Box ******************************
		var AreaTextBox = AreaBox.extend({
			type: "area-text-box",
			label: "Text Box",
			initialize: function (attributes) {
				AreaBox.prototype.initialize.apply(this, arguments);
			}
		});


		// ****************************** Preview Box ******************************
		var AreaPreviewBox = AreaBox.extend({
			type: "area-preview-box",
			label: "Preview Box",
			initialize: function (attributes) {
				AreaBox.prototype.initialize.apply(this, arguments);
			}
		});



		// ****************************** Box Area Component ******************************
		var BoxAreaComponent = kmc.KmcComponent.extend({
			type: "box-area",
			label: "Box Area",
			extra_defaults: {
				label: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				var _this = this;
				this.boxes = new AreaBoxes();

				var inital_boxes = this.get("boxes");
				_.each(inital_boxes, function (box) {
					if (box.post.post_type == "area-text-box") {
						var text_box = new AreaTextBox(box);
						text_box.set("area_id", _this.get("post").ID);
						_this.boxes.add(text_box);
					} else if (box.post.post_type == "area-preview-box") {
						var text_box = new AreaPreviewBox(box);
						text_box.set("area_id", _this.get("post").ID);
						_this.boxes.add(text_box);
					}
				});

				this.set("boxes", this.boxes);

				this.view = new BoxAreaComponentView({
					model: this
				});


				this.listenTo(this.boxes, "change", function (e) {
					_this.set("changed", true);
				});
			},

			to_json: function () {
				var json_boxes = this.boxes.toJSON();
				json_boxes = _.sortBy(json_boxes, 'position');
				return $.extend(this.toJSON(), {
					boxes: json_boxes
				});
			}
		});

		var BoxAreaComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-box-area-component-template").html()),
			no_rows_template: _.template($("#kmc-box-area-no-rows-template").html()),
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.listenTo(this.model.boxes, "add", this.on_box_added);

				this.render();
			},
			extra_events: {
				"click .save": "on_save_click",
				"click .add-box-btn": "on_add_box_click"
			},
			on_add_box_click: function () {
				var _this = this;
				var type = this.$(".add-box-select").val();
				if (type == "text-box") {
					new TextBoxDialog({
						callback: function (box_data) {
							var box = new AreaTextBox(box_data);
							_this.model.boxes.add(box);
							_this.model.set("changed", true);
							_this.model.trigger("change");
						}
					});
				} else {
					new PreviewBoxDialog({
						callback: function (box_data) {
							var box = new AreaPreviewBox(box_data);
							_this.model.boxes.add(box);
							_this.model.set("changed", true);
							_this.model.trigger("change");
						}
					});
				}
			},
			on_box_added: function (box) {
				if (this.model.boxes.length == 1) {
					this.$box_row_container.empty();
				}

				this.$box_row_container.append(box.row_view.$el);
			},
			on_save_click: function (e) {
				e.preventDefault();

				this.model.set_post_data({
					post_title: this.$(".post-title").val()
				});
				this.stop_editing();
			},

			update_positions: function () {
				this.$box_row_container.find("tr").each(function (index, element) {
					$(element).data("model").position = index;
					$(element).data("model").set("position", index);
				});
			},

			render: function () {
				var _this = this;
				this.render_base();

				if (this.model.get("edit")) {
					this.$box_row_container = this.$(".box-rows-container");

					var boxes = this.model.boxes;
					if (boxes.length) {
						_.each(boxes.models, function (box) {
							_this.$box_row_container.append(box.row_view.$el);
							box.row_view.delegateEvents();
						});
					} else {
						this.$box_row_container.html(
							this.no_rows_template()
						);
					}

					this.$box_row_container.sortable({
						handle: ".sort-handle",
						update: function(event, ui){
							_this.update_positions();
							_this.model.trigger("change");
						}
					});
				}
			}
		});

		window.KMC_MODULES_MODELS["box-area"] = BoxAreaComponent;

	});

}(jQuery, window, document));