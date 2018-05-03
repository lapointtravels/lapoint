define(["jquery"], function($) {
	"use strict";

	var MobileController = function() {

		var lang = $("html").attr("lang").substr(0,2);

		var readMoreText = [];
		readMoreText["en"] = "read more";
		readMoreText["no"] = "les mer";
		readMoreText["da"] = "l&#x00E6;s mere";
		readMoreText["sv"] = "l&#x00E4;s mer";


		function fixKmcIntroComponent() {
			var $intro = $(".kmc-component-intro-section");

			if( !$intro.length ) {
				return;
			}

			var $text = $intro.find( ".inner p" );

			$text.each( function(index, el ) {
				limitText( el, 196 );
				$(el).find("span").click( function(){
					$(this).parent().text( $(this).parent()[0].original );
				});
			});

			$intro.addClass( "show" );

		};

		function fixIngressText( componentClass ) {
			var $cmp = $( componentClass );
			if(!$cmp.length) {
				return;
			}

			var $text = $cmp.find("header .ingress p");
			$text.each(function(index, el) {
				if( limitText( el, 128 ) ) {
					$(el).find("span").click( function(){
						$(this).parent().text( $(this).parent()[0].original );
					});					
				}
			});
		}

		function limitText( el, limit ) {
			
			if( el.innerHTML.length < limit ) {
				console.log( el.innerHTML.length + " returning" );
				return false;
			}

			var span = $("<span>").innerHTML = "Read more";
			el.original = el.innerHTML;			
			el.innerHTML = el.innerHTML.substr(0, limit) + "...<span class='read-more-fix'> " + readMoreText[lang] + "</span>";

			return true;
		}

		//$( document ).ready( function() {
			fixKmcIntroComponent();
			fixIngressText( ".kmc-component-packages" );
			fixIngressText( ".kmc-component-camps" );
		//});

	};

	return MobileController;
	
});