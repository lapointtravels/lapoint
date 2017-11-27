(function ($, window, document, undefined) {

	$(function () {

		var LocationsComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "locations",
			label: "Location Rows",
			extra_defaults: {
				location: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new LocationsComponentView({
					model: this
				});
			},
			setup_preview_view: function () {
				this.preview_view = new LocationComponentPreviewView({
					model: this
				});
			}
		});

		var LocationsComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-locations-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			},

			on_edit_click: function (e) {
				e.preventDefault();
				this.model.set("edit", true);

				var current_location = this.model.get("location");
				if (!current_location) {
					this.model.set("location", this.$("#location").val());
					this.model.set("changed", true);
					console.log("...");
				}

			},
		});

		window.KMC_MODULES_MODELS["locations"] = LocationsComponent;

	});

}(jQuery, window, document));