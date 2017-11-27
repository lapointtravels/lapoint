(function ($, window, document, undefined) {

	$(function () {

		var InfoBoxComponent = kmc.KmcComponent.extend({
			type: "info-box",
			label: "Info Box",
			show_settings: false,
			extra_defaults: {
				button_text: "",
				button_link: "",
				icon: "surfer"
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new InfoBoxComponentView({
					model: this
				});
			}
		});

		var InfoBoxComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-info-box-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			},
			extra_events: {
				"change .post-content": "on_content_change",
			},

			on_content_change: function () {
				this.model.set_content(this.$(".post-content").val());
			}
		});

		window.KMC_MODULES_MODELS["info-box"] = InfoBoxComponent;

	});

}(jQuery, window, document));