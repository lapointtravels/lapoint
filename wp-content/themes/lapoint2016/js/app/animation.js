define(["jquery"], function($) {
	"use strict";

	var AnimationController = function () {
		var $win = $(window);
		var animation_elems = [];
		var throttled_func;

		$("[data-animated]").each(function (index, elem) {
			animation_elems.push($(elem));
		});

		function animate_after_delay ($el, delay) {
			setTimeout(function () {
				$el.addClass('in-view');
			}, delay);
		}

		function check_if_in_view () {
			var window_height = $win.height();
			var scroll_top = $win.scrollTop();
			var window_bottom_position = (scroll_top + window_height);

			for (var i=animation_elems.length-1; i>=0; i--) {
				var $element = animation_elems[i];
				var element_height = $element.outerHeight();
				var element_top_position = $element.offset().top;
				var element_bottom_position = (element_top_position + element_height);

				if ((element_bottom_position >= scroll_top) &&
					(element_top_position <= window_bottom_position)) {
					if ($element.data("in-view-callback")) {
						$element.data("in-view-callback")();
					} else if ($element.attr("data-delay")) {
						animate_after_delay($element, parseInt($element.attr("data-delay")));
					} else {
						$element.addClass('in-view');
					}

					animation_elems.splice(i, 1);
				}
			};

			if (!animation_elems.length) {
				$win.off('scroll', throttled_func);
				$win.off("resize", throttled_func);
			}
		}

		if (animation_elems.length) {
			throttled_func = _.throttle(check_if_in_view, 100);

			setTimeout(function () {
				$win.on('scroll', throttled_func);
				$win.on("resize", throttled_func);
				check_if_in_view();
			}, 200);
		}
	};

	return AnimationController;
});