(function ($, window, document, undefined) {

	$(function () {

		var BookingBarComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "booking-bar",
			label: "Booking Bar",
			extra_defaults: {
				default_destination_type: "",
				default_destination: "",
				default_camp: "",
				default_level: "",
				auto_search: 0
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				// Safety check since there already are stored components without the extra_defaults set..
				if (this.attributes.default_destination_type === undefined) {
					this.set({
						default_destination_type: "",
						default_destination: "",
						default_camp: "",
						default_level: "",
						auto_search: 0
					});
				}
				if (this.attributes.auto_search === undefined) {
					this.set("auto_search", 0);
				}

				this.view = new BookingBarComponentView({
					model: this
				});
			}
		});

		var BookingBarComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-booking-bar-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		window.KMC_MODULES_MODELS["booking-bar"] = BookingBarComponent;

	});

}(jQuery, window, document));