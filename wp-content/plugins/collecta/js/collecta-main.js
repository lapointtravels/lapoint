;(function ($, window, document, undefined) {

	$(document).ready(function($){

		var $name = $('#collecta-name'),
			$email = $('#collecta-email'),
			$submitBn = $("#collecta-submit"),
			lang = $('#collecta-lang').val();

		$submitBn.on('click', function(e){
			e.preventDefault();

			var name = $name.val(),
				email = $email.val(),
				formOk = true;

			/*if (!name || name == $name.attr("data-placeholder")){
				$name.css('color', '#ff0000').css('background-color', '#ffdddd');
				formOk = false;
			} else {
				$name.css('color', '').css('background-color', '');
			}*/

			if (!email || !validateEmail(email)){
				$email.css('color', '#ff0000').css('background-color', '#ffdddd');
				formOk = false;
			} else {
				$email.css('color', '').css('background-color', '');
			}

			if (formOk){

				$submitBn.fadeOut();
				$.post(CollectaAjax.ajaxurl, {
					action: 'submit-form',
					postNonce: CollectaAjax.postNonce,
					name: name,
					email: email,
					lang: lang
				}, function(data){

					if (data && data.status == 200){
						$("#collecta-container").fadeOut(500, function(){
							$("#collecta-thanks").fadeIn(500);
						});
					} else {
						$submitBn.show();
					}

				});

			}

		});

		$(".collecta-form").show();

		$(".collecta-field[data-placeholder]").each(function(){
			var placeholderText = $(this).attr("data-placeholder");
			$(this)
				.val(placeholderText)
				.on('focus', function(){
					if ($(this).val() == placeholderText) $(this).val('');
				})
				.on('blur', function(){
					if ($(this).val() === '') $(this).val(placeholderText);
				});
		});

	});

	function validateEmail(email) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

}(jQuery, window, document));