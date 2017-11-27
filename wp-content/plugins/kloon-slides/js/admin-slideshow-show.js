(function ($, window, document, undefined) {

	$(function () {

		// Use custom theme for PNotify
		PNotify.prototype.options.styling = {
			container: "notytheme",
			notice: "notytheme-notice",
			notice_icon: "notytheme-icon-notice",
			info: "notytheme-info",
			info_icon: "notytheme-icon-info",
			success: "notytheme-success",
			success_icon: "notytheme-icon-success",
			error: "notytheme-error",
			error_icon: "notytheme-icon-error"
		};

		// Slide Model
		var Slide = Backbone.Model.extend({
			default: {
				edit: false
			},

			initialize: function (attributes) {
				if (attributes.type == 'video') {
					this.view = new VideoSlideView({
						model: this
					});
				} else {
					this.view = new ImageSlideView({
						model: this
					});
				}
			},

			setData: function (key, val) {
				var data = this.get('data');
				data[key] = val;
				this.set('data', data);
			},

			sync: function (method, model, options) {
				var _this = this;

				if (method == "update") {
					var data = $.extend({
						action: "kloonslides_update_slide",
						slide_id: this.id,
					}, this.get('data'));

					$.post(ajaxurl, data, function (response) {
						if (response && response.status == 200){
							new PNotify({
							    title: 'Slide updated',
							    delay: 800,
							    type: 'success',
							    addclass: 'custom',
							    icon: 'success-icon'
							});

							_this.set(response.data);
							_this.set('edit', false);
						} else {
							alert("An error occured and the slide was not updated.. Please try again..");
						}
					});
				}

				// Delete slide
				else if (method == "delete") {
					if (confirm("Do you really want to delete this slide?")) {
						$.post(ajaxurl, {
							action: "kloonslides_delete_slide",
							slide_id: this.id
						}, function (response) {
							if (response.status == 200){
								_this.view.$el.slideUp(function () {
									_this.view.$el.remove();

								new PNotify({
								    title: 'Slide removed',
								    delay: 800,
								    type: 'success',
								    addclass: 'custom',
								    icon: 'success-icon'
								});

								});
							} else {
								alert("An error occured and the slide could not be deleted..");
							}
						});
					}
				}
			}
		});

		var AbstractSlideView = Backbone.View.extend({
			tagName: 'li',
			className: 'kloon-slide clearfix',
			initialize: function () {
				if (this.extraEvents) {
					this.events = $.extend(this.events, this.extraEvents);
				}

				this.presentationFieldsTemplate = _.template($('#presentation-fields-template').html());
				this.presentationEditFieldsTemplate = _.template($('#presentation-edit-fields-template').html());

				this.$el.attr('id', 'slide-' + this.model.get('id'));
				this.$el.attr('data-slide-id', this.model.get('id'));
				this.render();

				this.init();
			},

			init: function () {
				this.listenTo(this.model, 'change:presentation', this.updatePresentationFields.bind(this));

				var _this = this;
				this.listenTo(this.model, 'change:edit', function () {
					_this.render();
				});
				this.updatePresentationFields();
			},

			events: {
				'dblclick': 'onToggleEditClick',
				'click .btn-edit': 'onToggleEditClick',
				'click .btn-delete-slide': 'deleteSlide',
				'click .btn-update-slide': 'updateSlide',
				'change .slide-presentation-select': 'onPresentationChange'
			},

			onToggleEditClick: function (e) {
				e.preventDefault();
				this.model.set('edit', !this.model.get('edit'));
			},

			deleteSlide: function(e){
				this.model.destroy({
					wait: true
				});
			},

			onPresentationChange: function (e) {
				this.model.set('presentation', this.$('.slide-presentation-select').val());
			},

			updatePresentationFields: function () {
				this.$('.presentation-fields').html(
					this.presentationEditFieldsTemplate({
						slide: this.model.attributes,
						presentation: this.getPresentationData()
					})
				);
			},

			deleteSlide: function(e){
				this.model.destroy({
					wait: true
				});
			},

			getPresentationData () {
				console.error('getPresentationData must be overridden');
			},

			getCustomUpdateData () {
				console.error('getCustomUpdateData must be overridden');
			},

			getDataToUpdate: function (data) {
				return data;
			},

			updateSlide: function(e){
				var _this = this;
				var slide = this.model;
				var data = $.extend({}, slide.get('data'), {
					presentation: this.$('.slide-presentation-select').val()
				}, this.getCustomUpdateData());
				var presentationData = this.getPresentationData();

				if (presentationData) {
					_.each(presentationData.fields, function (field) {
						if (field.type === 'link') {
							data[field.key + '_label'] = _this.$('.presentation-' + field.key + '-label').val();
							data[field.key + '_url'] = _this.$('.presentation-' + field.key + '-url').val();
						} else {
							data[field.key] = _this.$('.presentation-' + field.key).val();
						}
					});
				}

				slide.set('data', data);
				slide.save();
			},

			render: function () {
				var isEditMode = this.model.get('edit');
				var template = (isEditMode) ? this.editTemplate : this.template;

				this.$el.toggleClass('edit-mode', !!isEditMode);

				this.$el.html(
					template({
						slide: this.model.attributes,
						presentation: this.getPresentationData(),
						presentationOutput: this.presentationFieldsTemplate({
							slide: this.model.attributes,
							presentation: this.getPresentationData()
						})
						// presentationFieldsTemplate: presentationFieldsTemplate
					})
				);

				if (isEditMode) {
					this.updatePresentationFields();
				}
			}
		});

		var ImageSlideView = AbstractSlideView.extend({
			initialize: function () {
				this.editTemplate = _.template($('#image-slide-edit-template').html());
				this.template = _.template($('#image-slide-template').html());

				AbstractSlideView.prototype.initialize.apply(this, arguments);
				this.$el.addClass('image-slide');
			},

			getPresentationData () {
				return window.kloonslides.settings.image_presentations[this.model.get('presentation')];
			},

			getCustomUpdateData () {
				return {}
			}
		});

		var VideoSlideView = AbstractSlideView.extend({
			initialize: function () {
				this.template = _.template($('#video-slide-template').html());
				this.editTemplate = _.template($('#video-slide-edit-template').html());
				this.customPresentationFieldsTemplate = _.template($('#video-presentation-fields-template').html());

				AbstractSlideView.prototype.initialize.apply(this, arguments);
				this.$el.addClass('video-slide');
			},

			init: function () {
				this.$videoId = this.$('.video-id');
				this.$width = this.$('.slide-width');
				this.$height = this.$('.slide-height');
				this.$autoplay = this.$('.video-autoplay');
				this.$keepProportions = this.$('.keep-proportions');

				AbstractSlideView.prototype.init.apply(this, arguments);
			},

			extraEvents: {
				'click .select-ogv-video': 'onSelectOgvVideoClick',
				'click .remove-ogv-video': 'onRemoveOgvVideoClick',
				'click .select-mp4-video': 'onSelectMp4VideoClick',
				'click .remove-mp4-video': 'onRemoveMp4VideoClick'
			},

			getPresentationData () {
				return window.kloonslides.settings.video_presentations[this.model.get('presentation')];
			},

			getCustomUpdateData () {
				return {
					video_id: this.$videoId.val(),
					width: this.$width.val(),
					height: this.$height.val(),
					autoplay: this.$autoplay.is(':checked'),
					keep_proportions: this.$keepProportions.is(':checked')
				}
			},

			updatePresentationFields: function (e) {
				AbstractSlideView.prototype.updatePresentationFields.apply(this, arguments);

				this.$('.custom-presentation-fields').html(
					this.customPresentationFieldsTemplate({
						slide: this.model.attributes,
						presentation: this.getPresentationData()
					})
				);

				this.delegateEvents();
			},

			onSelectOgvVideoClick: function (e) {
				e.preventDefault();
				this.selectVideo('ogv');
			},

			onSelectMp4VideoClick: function (e) {
				e.preventDefault();
				this.selectVideo('mp4');
			},

			selectVideo: function (type) {
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
					_this.model.setData('background_video_' + type, video);
					_this.updatePresentationFields();
					// _this.settings["background_video_" + type] = video;
					// _this.render();
				});
			},

			onRemoveOgvVideoClick: function (e) {
				e.preventDefault();
				this.model.setData('background_video_ogv', false);
				//this.settings["background_video_ogv"] = false;
				//this.render();
				this.updatePresentationFields();
			},

			onRemoveMp4VideoClick: function (e) {
				e.preventDefault();
				this.model.setData('background_video_mp4', false);
				// this.settings["background_video_mp4"] = false;
				// this.render();
				this.updatePresentationFields();
			},

			/*getDataToUpdate: function (data) {

			},*/


			/*render: function () {
				this.$el.html(
					this.template({
						slide: this.model.attributes
					})
				);
			}*/
		});


		var Slides = Backbone.Collection.extend({
			model: Slide
		});

		// ****************************** SlideShowView ******************************
		var SlideshowView = Backbone.View.extend({
			initialize: function (attributes) {
				var _this = this;

				this.slideshow = attributes.data;
				this.$slidesContainer = this.$('#slides-container');
				this.slides = new Slides(window.kloonslides.slides);
				this.slides.on('add', this.addSlide, this);
				this.slides.on('remove', this.removeSlide, this);

				this.$btnSaveSettings = this.$('.btn-save-settings');
				this.$saveMessage = $('#kloonslides-save-message');

				this.$btnAddVideo = this.$('.btn-add-video');
				this.$addVideoMessage = $('#add-video-message');
				this.$videoType = this.$('#video-type');
				this.$videoId = this.$('#video-id');
				this.$videoWidth = this.$('#video-width');
				this.$videoHeight = this.$('#video-height');

				this.$settingTimer = this.$('#slideshow-timer');
				this.$settingSize = this.$('#slideshow-size');
				this.$settingFixedHeightPx = this.$('#slideshow-fixed-height-px');
				this.$settingFixedHeightTablet = this.$('#slideshow-fixed-height-tablet');
				this.$settingFixedHeightPhone = this.$('#slideshow-fixed-height-phone');
				this.$settingHideNav = this.$('#slideshow-hide-nav');

				// Add all initial slides
				_.each(this.slides.models, function (slide) {
					_this.$slidesContainer.append( slide.view.$el );
				});

				this.setupSorting()
			},

			events: {
				'click .select-image': 'onSelectImageClick',
				'click .btn-save-settings': 'saveSettings',
				'click .btn-add-video': 'onAddVideoClick'
			},


			onSelectImageClick: function (e) {
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
					var uploadedImage = image.state().get('selection').first();
					var imageData = uploadedImage.toJSON();
					var img = {
						thumbnail: imageData["sizes"]["thumbnail"].url,
						sizes: {}
					};

					_.each(window.kloonslides.settings.media_library_sizes, function (obj, size) {
						// i.e: size = "lg" & obj.name = "image-2000"
						img.sizes[obj.name] = imageData["sizes"][obj.name];
					});

					$.post(ajaxurl, {
						action: 'kloonslides_add_image_slide',
						slideshow_id: _this.slideshow.id,
						data: img
					}, function (response) {
						if (response && response.status == 200 && response.data) {
							_this.slides.add(new Slide(response.data));

							new PNotify({
							    title: 'Image slide added',
							    delay: 800,
							    type: 'success',
							    addclass: 'custom',
							    icon: 'success-icon'
							});
						} else {
							alert("An error occured and the setting was not updated.. Please try again..");
						}
					});

				});
			},

			onAddVideoClick: function (e) {
				e.preventDefault();
				var _this = this;

				this.$btnAddVideo.attr('disabled', 'disabled');
				this.$addVideoMessage.show();

				var video_type = this.$videoType.val();
				var video_id = this.$videoId.val();
				var width = this.$videoWidth.val();
				var height = this.$videoHeight.val();

				var data = {
					action: 'kloonslides_add_video_slide',
					slideshow_id: this.slideshow.id,
					video_type: video_type,
					video_id: video_id,
					width: width,
					height: height
				};

				$.post(ajaxurl, data, function(response) {
					_this.$btnAddVideo.removeAttr('disabled');
					_this.$addVideoMessage.fadeOut();

					if (response && response.status == 200 && response.data) {
						_this.slides.add(new Slide(response.data));

						new PNotify({
							    title: 'Video slide added',
							    delay: 800,
							    type: 'success',
							    addclass: 'custom',
							    icon: 'success-icon'
							});
					} else {
						alert('An error occured..');
					}
				});

				return false;
			},

			saveSettings: function (e) {
				e.preventDefault();
				e.stopPropagation();

				this.$btnSaveSettings.attr('disabled', 'disabled');
				this.$saveMessage.show();

				var _this = this;
				$.post(ajaxurl, {
					action: 'kloonslides_update_slideshow_settings',
					slideshow_id: this.slideshow.id,
					timer: this.$settingTimer.val(),
					size: this.$settingSize.val(),
					fixed_height_px: this.$settingFixedHeightPx.val(),
					fixed_height_tablet: this.$settingFixedHeightTablet.val(),
					fixed_height_phone: this.$settingFixedHeightPhone.val(),
					hide_nav: this.$settingHideNav.is(':checked'),
				}, function (response) {

					if (response && response.status == 200) {
						_this.$btnSaveSettings.removeAttr('disabled');
						_this.$saveMessage.fadeOut();

						new PNotify({
						    title: 'Settings saved',
						    delay: 800,
						    type: 'success',
						    addclass: 'custom',
						    icon: 'success-icon'
						});

					} else {
						alert('An error occured and the setting was not updated.. Please try again..');
					}

				});

				return false;
			},

			setupSorting: function () {
				var _this = this;
				this.$slidesContainer.sortable({
					handle: '.sort-handle',
					update: function (event, ui) {
						_this.savePosition();
					}
				});
				return this;
			},

			addSlide: function(slide){
				this.$slidesContainer.append(slide.view.$el);
			},

			removeSlide: function(slide){
				this.savePosition();
			},

			savePosition: function() {

				var ids = [];
				this.$slidesContainer.find('.kloon-slide').each(function (index) {
					ids.push($(this).attr('data-slide-id'));
				});

				$.post(ajaxurl, {
					action: 'kloonslides_update_slide_position',
					ids: ids.join(',')
				}, function(response) {
					if (!response || response.status != 200){
						alert('An error occured and the new position could not be saved..');
					} else {
						new PNotify({
						    title: 'Order saved',
						    delay: 800,
						    type: 'success',
						    addclass: 'custom',
						    icon: 'success-icon'
						});
					}
				});
			}
		});


		new SlideshowView({
			el: $('#poststuff'),
			data: window.kloonslides.slideshow
		});
	});

}(jQuery, window, document));
