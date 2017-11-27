;(function ($, window, document, undefined){

	$(document).ready(function($){
		console.log("!!!!");

		$(".update-code-button").on("click", function (e) {
			e.preventDefault();
			var $btn = $(e.currentTarget);
			var $tr = $btn.closest("tr");
			var post_id = $tr.attr("data-post-id");
			var booking_code = $tr.find(".booking-code").val();
			var $saving = $tr.find(".info-saving");
			var $saved = $tr.find(".info-saved");


			$btn.addClass("hidden");
			$saving.removeClass("hidden");

			var data = {
				action: "update_booking_code",
				post_id: post_id,
				booking_code: booking_code
			};

			if ($tr.hasClass("has-label-override")) {
				data["has_booking_label"] = "1";
				data["en_booking_label"] = $tr.find(".booking-label.en").val();
				data["sv_booking_label"] = $tr.find(".booking-label.sv").val();
				data["nb_booking_label"] = $tr.find(".booking-label.nb").val();
				data["da_booking_label"] = $tr.find(".booking-label.da").val();
			}

			$.post(ajaxurl, data, function (response) {
				if (response.status == 200) {
					$saving.addClass("hidden");
					$saved.removeClass("hidden");
					setTimeout(function () {
						$saved.addClass("hidden");
						$btn.removeClass("hidden");
					}, 2000);

				} else {
					alert("Something went wrong...");
					$saving.addClass("hidden");
					$btn.removeClass("hidden");
				}
			});
		});

		$(".override-label-link").on("click", function (e) {
			e.preventDefault();
			var $td = $(e.currentTarget).closest("td");
			$td.addClass("open");
		});

	});

}(jQuery, window, document));