(function ($, window, document, undefined) {

	$(function () {

		var VideoRow = Backbone.Model.extend({
			initialize: function (attributes) {
				this.view = new VideoRowTemplate({
					model: this
				});
			},

			remove: function () {
				this.view.$el.remove();
				this.collection.remove(this);
			}
		});

		var VideoRowTemplate = Backbone.View.extend({
			template: _.template($('#kmc-video-component-row-template').html()),
			tagName: 'li',

			initialize: function () {
				this.render();
			},

			events: {
				'click .remove-video': 'on_remove_click'
			},

			on_remove_click: function () {
				this.model.remove();
			},

			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		var VideoRows = Backbone.Collection.extend({
			model: VideoRow
		});



		// ****************************** Preview Box Dialog ******************************
		var AddVideoDialog = Backbone.View.extend({
			template: _.template($("#kmc-video-component-modal-template").html()),
			contentTemplate: _.template($("#kmc-video-component-modal-content-template").html()),

			initialize: function (attributes) {
				var _this = this;
				this.callback = attributes.callback;

				this.$el.html(this.template());
				this.$content = this.$(".content");
				$("body").append(this.$el);

				this.$modal = this.$(".md-modal");
				this.$modal.addClass("md-show");

				this.state = {
					loading: true,
					error: false,
					availableVideos: []
				};
				this.renderContent();

				// Load available videos
				$.post(ajaxurl, {
					action: "module_action",
					module: "video_slider",
					module_action: "get_available_videos"
				}, function (response) {
					_this.state.loading = false;
					if (response.status == 200) {
						_this.state.availableVideos = response.videos;
					} else {
						_this.state.error = true;
					}
					_this.renderContent();
				});
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
				var videoId = this.$('.video-select').val();
				video = _.find(this.state.availableVideos, function (video) {
					return video.id === videoId;
				});
				this.callback(video);
				this.close_dialog();
			},

			close_dialog: function () {
				var _this = this;
				this.$modal.removeClass("md-show");

				setTimeout(function () {
					_this.$el.remove();
				}, 500);
			},

			renderContent: function () {
				this.$content.html(this.contentTemplate(this.state));
			}
		});



		// ****************************** Component ******************************
		var VideosComponent = kmc.KmcComponent.extend({
			edit_when_added: true,
			type: "video_slider",
			label: "Video slider",
			extra_defaults: {
				videos: []
			},

			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.set('videos', new VideoRows(this.get('videos')));

				this.view = new VideosComponentView({
					model: this
				});

				var _this = this;
				this.listenTo(this.get('videos'), 'remove', function () {
					_this.set('changed', true);
				});
			}
		});

		var VideosComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-videos-component-template").html()),
			base_only: false,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",

			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render();
			},

			extra_events: {
				'click .add-video': 'on_add_video_click'
			},

			on_add_video_click: function () {
				var _this = this;

				new AddVideoDialog({
					callback: function (videoData) {

						$.post(ajaxurl, {
							action: "module_action",
							module: "video_slider",
							module_action: "get_video",
							video_id: videoData.id
						}, function (response) {
							if (response.status == 200) {
								var video = new VideoRow(response.video);
								_this.model.get("videos").add(video);
								_this.model.set("changed", true);
								_this.model.trigger("change");

								_this.$videosList.append(video.view.$el);
							}
						});
					}
				});
			},

			render: function () {
				this.render_base();

				if (this.model.get('edit')) {
					var _this = this;
					this.$videosList = this.$('.videos-list');

					_.each(this.model.get("videos").models, function (video) {
						_this.$videosList.append(video.view.$el);
					});

				}

				this.delegateEvents();

			}
		});

		window.KMC_MODULES_MODELS["video_slider"] = VideosComponent;

	});

}(jQuery, window, document));
