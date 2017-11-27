(function ($, window, document, undefined) {

	$(function () {

		var PostsSliderComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "posts-slider",
			label: "Post slider",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new PostsSliderComponentView({
					model: this
				});
			}
		});

		var PostsSliderComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-posts-slider-component-template").html()),
			base_only: true,
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			},
			extra_events: {
				"change .post-title": "on_title_change",
			},

			on_title_change: function (e) {
				this.model.set_title(this.$(".post-title").val());
			}
		});

		window.KMC_MODULES_MODELS["posts-slider"] = PostsSliderComponent;

	});

}(jQuery, window, document));