define(["jquery"], function($) {
	"use strict";

	var MobileController = function() {

		var lang = $("html").attr("lang").substr(0,2);

		var readMoreText = [];
		readMoreText["en"] = "Read more";
		readMoreText["no"] = "Les mer";
		readMoreText["da"] = "L&#x00E6;s mere";
		readMoreText["sv"] = "L&#x00E4;s mer";


		function fixKmcIntroComponent() {
			var $intro = $(".kmc-component-intro-section");

			if( !$intro.length ) {
				return;
			}

			var $text = $intro.find( ".inner p" );

			$text.each( function(index, el ) {
				limitText( el );
				$(el).find("span").click( function(){
					$(this).parent().text( $(this).parent()[0].original );
				});
			});

			$intro.addClass( "show" );

		};

		function limitText( el ) {
			var span = $("<span>").innerHTML = "Read more";
			el.original = el.innerHTML;			
			el.innerHTML = el.innerHTML.substr(0, 112) + "...<span> " + readMoreText[lang] + "</span>";
		}

		//$( document ).ready( function() {
			fixKmcIntroComponent();			
		//});

	};

	return MobileController;
	
});