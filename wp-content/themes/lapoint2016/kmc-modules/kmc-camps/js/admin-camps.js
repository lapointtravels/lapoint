(function ($, window, document, undefined) {

	$(function () {

		var CampsComponent = kmc.KmcComponent.extend({
			edit_when_added: false,
			type: "camps",
			label: "Camp Rows",
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				this.view = new CampsComponentView({
					model: this
				});
			},
			setup_preview_view: function () {
				this.preview_view = new CampComponentPreviewView({
					model: this
				});
			}
		});

		var CampsComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-camps-component-template").html()),
			base_only: true,
			auto_set_title: ".post-title",
			auto_set_content: ".post-content",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.render_base();
			}
		});

		var CampComponentPreviewView = Backbone.View.extend({
			template: _.template($("#kmc-camps-preview-template").html()),
			initialize: function () {
				this.listenTo(this.model, "change", this.render);
				this.render();
			},
			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		window.KMC_MODULES_MODELS["camps"] = CampsComponent;

	});

}(jQuery, window, document));