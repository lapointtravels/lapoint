(function ($, window, document, undefined) {

	$(function () {

		var Tab = Backbone.Model.extend({
			defaults: {
				title: "New Tab"
			},
			initialize: function (attributes) {
				if (!this.get("components")) {
					this.set("components", []);
				}

				this.set("guid", Math.floor(Math.random() * 1000000));

				this.components = new kmc.KmcComponents();

				this.view = new TabView({
					model: this
				});
				this.edit_view = new TabEditView({
					model: this
				});
				this.flap_view = new TabFlapView({
					model: this
				});


				var _this = this;
				if (attributes && attributes.components) {
					_.each(attributes.components, function (component) {
						_this.add_component(component);
					});
				}

				var _this = this;
				this.listenTo(this.components, "change", function () {
					_this.trigger("change");
				});

			},

			add_component: function (component, edit_mode) {
				if (KMC_MODULES_MODELS[component.post.post_type]) {
					if (edit_mode === true) {
						$.extend(component, { edit: true });
					}
					var comp = new KMC_MODULES_MODELS[component.post.post_type](component);
					comp.setup_preview_view();
					this.components.add(comp);
				} else {
					console.error("Could not find component for type: " + component.post.post_type);
				}
			},
			add_component_type: function (type) {
				var component = new KMC_MODULES_MODELS[type]();
				component.setup_preview_view();
				this.components.add(component);
				this.trigger("change");
			},

			remove_component: function (component) {
				this.components.remove(component);
			},

			remove: function () {
				this.trigger("change");
				this.collection.remove(this);
			},

			rebind_events: function () {
				this.edit_view.delegateEvents();
				_.each(this.components.models, function (component) {
					component.view.delegateEvents();
					if (typeof(component.view.refresh) !== "undefined") {
						component.view.refresh();
					}
				});
			},

			to_json: function () {
				this.components.models = _.sortBy(this.components.models, 'position');
				return {
					components: _.map(this.components.models, function (component) {
						if (component.saved) {
							return component.get("post").ID;
						} else {
							return component.to_json();
						}

					}),
					title: this.get("title")
				}
			}
		});


		var TabFlapView = Backbone.View.extend({
			tagName: "li",
			initialize: function () {
				this.render();

				this.listenTo(this.model, "change:title", this.render);
			},
			render: function () {
				var title = this.model.get("title") || "-";
				this.$el.html(
					"<a href='#tab-" + this.model.get("guid") + "'>"+ title +"</a>"
				);
			}
		});
		var TabEditView = Backbone.View.extend({
			template: _.template($("#kmc-tab-edit-template").html()),
			initialize: function () {
				this.$el.attr("id", "tab-" + this.model.get("guid"));

				this.render();
				this.$container = this.$(".tab-components");

				this.listenTo(this.model.components, "add", this.on_component_added);
			},
			events: {
				"change .tab-title": "on_title_change",
				"click .add-tab-component": "on_add_tab_component_click",
				"click .remove-tab": "on_remove_tab_click"
			},

			on_add_tab_component_click: function (e) {
				e.preventDefault();
				var _this = this;
				new kmc.NewComponentDialog({
					sub_only: true,
					callback: function (type) {
						_this.model.add_component_type(type);
					}
				});
			},

			on_remove_tab_click: function (e) {
				e.preventDefault();
				this.model.remove();
			},

			on_title_change: function (e) {
				this.model.set("title", this.$(".tab-title").val());
				this.model.set("changed", true);
			},

			on_component_added: function (component) {
				this.$container.append(component.view.$el);
			},

			render: function () {
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		var TabView = Backbone.View.extend({
			template: _.template($("#kmc-tab-template").html()),
			initialize: function () {
				this.$el.attr("id", "tab-" + this.model.get("guid"));

				this.render();
				this.$container = this.$(".tab-components");

				this.listenTo(this.model.components, "add", this.on_component_added);
			},

			on_component_added: function (component) {
				this.$container.append(component.preview_view.$el);
			},

			render: function () {
				console.log("RENDER TAB VIEW");
				this.$el.html(
					this.template(this.model.attributes)
				);
			}
		});

		var Tabs = Backbone.Collection.extend({
			model: Tab,
			comparator: "position",
			to_json: function () {
				var tabs = _.map(this.models, function (tab) {
					return tab.to_json();
				});
				return tabs;
			}
		});



		// ****************************** Component ******************************
		var TabsComponent = kmc.KmcComponent.extend({
			type: "tabs",
			label: "Tabs",
			extra_defaults: {
				label: ""
			},
			initialize: function (attributes) {
				kmc.KmcComponent.prototype.initialize.apply(this, arguments);

				var initial_tabs = this.get("tabs");
				var tabs = new Tabs();
				_.each(initial_tabs, function (tab_data) {
					var tab = new Tab(tab_data);
					tabs.add(tab);
				});
				this.set("tabs", tabs);

				var _this = this;
				this.listenTo(tabs, "change", function () {
					_this.set("changed", true);
				});


				this.view = new TabsComponentView({
					model: this
				});
			},
			add_tab: function () {
				var tabs = this.get("tabs");
				var tab = new Tab();
				tabs.add(tab);

				this.set("changed", true);
				this.trigger("change:tabs");
			},
			update_tab_position: function () {
				_.each(this.get("tabs").models, function (tab) {
					tab.set("position", tab.flap_view.$el.index());
				});

				this.get("tabs").sort();
			},
			to_json: function () {
				return $.extend(this.toJSON(), {
					tabs: this.get("tabs").to_json()
				});
			}
		});

		var TabsComponentView = kmc.KmcComponentView.extend({
			template: _.template($("#kmc-tabs-component-template").html()),
			auto_set_title: ".post-title.tabs-title",
			initialize: function () {
				kmc.KmcComponentView.prototype.initialize.apply(this, arguments);

				this.listenTo(this.model.get("tabs"), "add", this.on_tab_added);
				this.listenTo(this.model.get("tabs"), "remove", this.render);
				this.listenTo(this.model.get("tabs"), "change:title", function () {
					this.$(".tabs").tabs("refresh");
				});
				this.render();
			},
			extra_events: {
				"click .add-tab": "on_add_tab_click"
			},

			on_add_tab_click: function (e) {
				e.preventDefault();

				this.model.add_tab();
			},

			on_tab_added: function (tab) {
				this.$(".add-btn-container").before(tab.flap_view.$el);
				this.$(".tabs").append(tab.edit_view.$el).tabs("refresh");
			},

			make_sortable: function () {
				var _this = this;
				this.$(".tab-flaps").sortable({
					items: "li:not(.add-btn-container)",
					update: function (event, ui) {
						_this.model.update_tab_position();
					}
				});
			},

			render: function () {
				var _this = this;

				this.render_base();

				console.log("RENDER");

				if (this.model.get("edit")) {

					_.each(this.model.get("tabs").models, function (tab) {
						_this.$(".add-btn-container").before(tab.flap_view.$el);
						_this.$(".tabs").append(tab.edit_view.$el);

						//tab.edit_view.delegateEvents();
						tab.rebind_events();
					});

					this.$(".tabs").tabs({
	  					/*activate: function(event, ui) {
	  						//_this.render();
	  					}*/
					});

					this.make_sortable();

				} else {

					console.log("CHECK *");

					var $tabsList = this.$(".tabs ul");
					var $tabsContainer = this.$(".tabs");

					console.log($tabsList, $tabsContainer);

					_.each(this.model.get("tabs").models, function (tab) {
						$tabsList.append(tab.flap_view.$el);
					});

					console.log("CHECK **");

					_.each(this.model.get("tabs").models, function (tab) {
						console.log(tab.view.$el);
						$tabsContainer.append(tab.view.$el);
						//_this.$(".tabs ul").after(tab.view.$el);
					});

					this.$(".tabs").tabs();

				}
			}
		});

		window.KMC_MODULES_MODELS["tabs"] = TabsComponent;

	});

}(jQuery, window, document));