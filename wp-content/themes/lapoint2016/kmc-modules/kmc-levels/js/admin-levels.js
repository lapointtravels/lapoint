(function ($, window, document, undefined) {

	$(function () {

		var LevelsComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "levels",
			label: "Level Boxes",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new LevelsComponentView({
					model: this
				});
			}
		});

		var LevelsComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-levels-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["levels"] = LevelsComponent;

	});

}(jQuery, window, document));