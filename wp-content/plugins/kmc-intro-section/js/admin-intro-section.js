(function ($, window, document, undefined) {

	$(function () {

		var IntroSectionComponent = kmc.KmcComponent.extend({
			type: "intro-section",
			label: "Intro Section",
			extra_defaults: {
				col1_title: "",
				col1_content: "",
				col2_title: "",
				col2_content: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new IntroSectionComponentView({
					model: this
				});
			}
		});

		var IntroSectionComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-intro-section-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["intro-section"] = IntroSectionComponent;

	});

}(jQuery, window, document));