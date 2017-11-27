;(function ($, window, document, undefined){

	$(document).ready(function($){

		// Add easing options
		var sel = $("#slide-show-easing"),
			sel_val = sel.val();
		$.each( $.easing, function(name, impl){
			if ($.isFunction(impl) && !/jswing/.test(name) && name != sel_val) sel.append( $("<option></option>").val(name).text(name) );
		});

		// Slide Model
		var Slide = Backbone.Model.extend({
			defaults: {
				position: 0,
				filename: "",
				slide_type: 1,
				id: "-",
				title: "",
				text1: "",
				text2: "",
				text3: "",
				vertical_align: "center",
				link: "",
				thumb_url: "",
				video_type: "",
				video_id: "",
				autoplay: 1,
				keep_proportions: 1,
				width: 0,
				height: 0,
				image_data: false
			},

			initialize: function (attributes) {
				console.log(attributes);
				this.view = new SlideView({
					model: this
				});
			},

			is_video: function () {
				return this.get("video_type") ? true : false;
			},

			is_youtube: function () {
				return this.get("video_type") == "youtube";
			},

			is_vimeo: function () {
				return this.get("video_type") == "vimeo";
			},

			sync: function (method, model, options) {
				var _this = this;

				if (method == "update"){
					var	data = {
						action: "update_slide",
						slide_id: this.id,
						title: model.attributes.title,
						link: model.attributes.link,
						slide_type: model.attributes.slide_type,
						text1: model.attributes.text1,
						text2: model.attributes.text2,
						text3: model.attributes.text3,
						vertical_align: model.attributes.vertical_align,
						video_type: model.attributes.video_type,
						video_id: model.attributes.video_id,
						autoplay: model.attributes.autoplay,
						keep_proportions: model.attributes.keep_proportions,
						width: model.attributes.width,
						height: model.attributes.height
					};
					$.post(ajaxurl, data, function(response){
						if (response == 200){
							$("#slide-"+ data.slide_id +"-updated").fadeIn("slow", function(){
								$("#slide-"+ data.slide_id +"-updated").fadeOut("slow");
							});
						} else {
							alert("An error occured and the slide was not updated.. Please try again..");
						}
					});
				}

				// Delete slide
				else if (method == "delete"){
					if (confirm("Do you really want to delete this slide?")){
						$.post(ajaxurl, {action: "delete_slide", slide_id: this.id }, function(response) {
							if (response == 200){
								_this.view.$el.slideUp(function () {
									_this.view.$el.remove();
								});
							} else {
								alert("An error occured and the slide could not be deleted..");
							}
						});
					}
				}
			}
		});

		var SlideView = Backbone.View.extend({
			template: _.template($('#slide-template').html()),
			tagName: "li",
			initialize: function () {
				this.$el.attr("id", "slide-" + this.model.get("id"));
				this.$el.addClass("imageslider-slide clearfix");
				this.render();

				this.updateOptions();
			},

			events: {
				"click .delete-slide-button":  "deleteSlide",
				"click .update-slide-button": "updateSlide",
				"change .slide-type-select": "updateOptions"
			},

			updateOptions: function(e) {
				var $slide_type_select = this.$(".slide-type-select");
				if (!$slide_type_select.length) return;

				var $ul = this.$(".edit-slide-info");
				var id = $slide_type_select.val();
				var slide_type = _.find(IMAGE_SLIDER_SETTINGS.slide_types, function (o) {
					return o.id == id;
				});

				if (slide_type.title) {
					$ul.find(".li-title").show();
				} else {
					$ul.find(".li-title").hide();
				}
				if (slide_type.link) {
					$ul.find(".li-link").show();
				} else {
					$ul.find(".li-link").hide();
				}

				$ul.find(".li-text").hide();
				_.each(slide_type.choices, function (c, i) {
					var $txt_li = $ul.find(".li-text-" + (i + 1));
					$txt_li.find("label").text(c);
					$txt_li.show();
				});
			},
			deleteSlide: function(e){
				this.model.destroy({
					wait: true
				});
			},
			updateSlide: function(e){
				var _this = this;
				var slide = this.model;

				var data = {
					width: this.$(".slide-width").val(),
					height: this.$(".slide-height").val()
				}

				if (slide.is_video()) {

					// Video
					$.extend(data, {
						video_type: this.$(".video-type").val(),
						video_id: this.$(".video-id").val(),
						autoplay: this.$(".video-autoplay").is(":checked"),
						keep_proportions: this.$(".video-keep-proportions").is(":checked")
					});

				} else {

					var slide_type_id = this.$(".slide-type-select").val(),
						slide_type = _.find(IMAGE_SLIDER_SETTINGS.slide_types, function (o) {
							return o.id == slide_type_id;
						});

					// Image
					var txt_vals = ["", "", ""];
					if (slide_type_id) {
						_.each(slide_type.choices, function (c, i) {
							txt_vals[i] = _this.$(".slide-text-" + (i + 1)).val();
						});
					}

					$.extend(data, {
						title: slide_type ? this.$(".slide-title").val() : "",
						link: slide_type ? this.$(".slide-link").val() : "",
						slide_type: slide_type_id,
						text1: txt_vals[0],
						text2: txt_vals[1],
						text3: txt_vals[2],
						vertical_align: this.$(".vertical-align").val() || ""
					});
				}


				slide.save(data);
			},

			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		// Slides collection
		var Slides = Backbone.Collection.extend({
			model: Slide
		});


		// ****************************** SlideShowView ******************************
		var SlideShowView = Backbone.View.extend({
			initialize: function (attributes) {
				var _this = this;
				//this.template = _.template($('#slides-template').html());
				//this.inner_template = _.template($('#slide-template').html());

				this.$container = this.$("#edit-slides-list");

				this.setup_plupload();

				this.collection.on("add", this.addSlide, this);
				this.collection.on("remove", this.removeSlide, this);

				// Add all initial slides
				_.each(this.collection.models, function (slide) {
					_this.$container.append( slide.view.$el );
				});

				this.render();
				this.setupSorting()
			},

			setup_plupload: function () {
				// Setup Plupload
				var pluploader = new Pluploader($, {
					url: $('#plupload-upload-path').val(),
					flash_swf_url: $('#plupload-flash-url').val(),
					multipart_params: {
						slide_show_id: slide_show_id,
						wp_prefix: wp_prefix
					}
				}).init();

				$(pluploader).on("PluploadFileUploaded", function(event, json_response){
					slide_show_view.collection.add({
						id: json_response.id,
						thumb_url: json_response.thumb_url,
						filename: json_response.filename,
						type: json_response.type
					});
				});
			},

			events: {
				"click .select-image": "on_select_image_click",
				"submit #form-update-imsl-settings": "save_settings",
				"submit #form-imsl-video": "add_video_slide"
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
					img.thumbnail = img_data["sizes"]["thumbnail"].url;
					img.sizes = {};

					_.each(IMAGE_SLIDER_SETTINGS.media_library_sizes, function (size) {
						img.sizes[size] = img_data["sizes"][size];
					});

					/*

					_this.background_image = img;*/
					console.log("Image data", img);
					/*_this.model.set("image", {
						sizes: img_data.sizes,
						url: img_data.url
					});
					_this.model.set("changed", true);
					console.log("Attributes: ", _this.model.attributes);
					//_this.render_image_row();
					_this.render_base();*/

					$.post(ajaxurl, {
						action: "add_image_slide",
						slide_show_id: _this.$("#slide-show-id").val(),
						data: img
					}, function (response) {
						if (!response || !response.id){
							alert("An error occured and the setting was not updated.. Please try again..");
						} else {
							slide_show_view.collection.add({
								id: response.id,
								image_data: img
								// thumb_url: response.thumb_url,
								// filename: response.filename,
								// type: response.type
							});
						}
					});

				});
			},

			add_video_slide: function (e) {
				e.preventDefault();
				var _this = this;
				this.$("#imsl-update-video-submit").attr("disabled", "disabled");
				this.$("#imsl-update-video-saving").show();

				var video_type = this.$("#video-type").val();
				var video_id = this.$("#video-id").val();
				var width = this.$("#video-width").val();
				var height = this.$("#video-height").val();

				var data = {
					action: "add_video_slide",
					slide_show_id: this.$("#slide-show-id").val(),
					video_type: video_type,
					video_id: video_id,
					width: width,
					height: height
				};

				$.post(ajaxurl, data, function(response) {
					_this.$("#imsl-update-video-submit").removeAttr("disabled");
					_this.$("#imsl-update-video-saving").fadeOut();
					if (!response || !response.id){
						alert("An error occured and the setting was not updated.. Please try again..");
					} else {
						_this.collection.add({
							id: response.id,
							video_id: video_type,
							video_id: video_id,
							width: width,
							height: height
						});
					}
				});
			},

			save_settings: function (e) {
				e.preventDefault();
				this.$("#imsl-update-settings-submit").attr("disabled", "disabled");
				this.$("#imsl-update-settings-saving").show();

				var data = {
					action: "update_slide_show_settings",
					slide_show_id: this.$("#slide-show-id").val(),
					title: this.$("#slide-show-title").val(),
					timer: this.$("#slide-show-timer").val(),
					size: this.$("#slide-show-size").val()
				};

				var _this = this;
				$.post(ajaxurl, data, function(response) {
					_this.$("#imsl-update-settings-submit").removeAttr("disabled");
					_this.$("#imsl-update-settings-saving").fadeOut();
					if (response != 200){
						alert("An error occured and the setting was not updated.. Please try again..");
					}
				});
			},

			setupSorting: function(){
				var _this = this;
				this.$container.sortable({
					update: function(event, ui){
						_this.savePosition();
					}
				});
				return this;
			},

			addSlide: function(slide){
				//$('ul#edit-slides-list', this.el).append( this.inner_template(slide.toJSON()) );
				this.$container.append( slide.view.$el );
			},

			removeSlide: function(slide){
				//var _this = this;
				//$("#slide-"+ slide.id, this.el).slideUp(function(){
				//	$("#slide-"+ slide.id, this.el).remove();
					this.savePosition();
				//});
			},

			savePosition: function(){
				var ids = [];
				this.$container.find("> li").each(function(index){
					ids.push( $(this).attr("id").split("-")[1] );
				});
				$.post(ajaxurl, {action: "update_slide_position", ids: ids.join(",") }, function(response) {
					if (response != 200){
						alert("An error occured and the new position could not be saved..");
					}
				});
			},

			render: function(){
				/*var _this = this;
				this.$el.html( this.template( {slides: this.collection.toJSON()} ) );
				this.$(".imageslider-slide").each(function(index, elem){
					_this.updateOptionsFor($(elem).attr("data-slide-id"));
				});*/
				return this;
			}
		});



		// ****************************** Init ******************************

		// Create the backbone view
		var slide_show_view = new SlideShowView({
			el: $("#slideshow-page"),
			collection: new Slides(slides_json)
		});

	});

}(jQuery, window, document));