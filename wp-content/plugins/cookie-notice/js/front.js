( function ( $ ) {

	// set Cookie Notice
	$.fn.setCookieNotice = function ( cookie_value ) {
		if ( cnArgs.onScroll === 'yes' ) {
			$( window ).off( 'scroll', cnHandleScroll );
		}

		var cnTime = new Date(),
			cnLater = new Date(),
			cnDomNode = $( '#cookie-notice' ),
			cnSelf = this;

		// set expiry time in seconds
		cnLater.setTime( parseInt( cnTime.getTime() ) + parseInt( cnArgs.cookieTime ) * 1000 );

		// set cookie
		cookie_value = cookie_value === 'accept' ? true : false;
		document.cookie = cnArgs.cookieName + '=' + cookie_value + ';expires=' + cnLater.toGMTString() + ';' + ( cnArgs.cookieDomain !== undefined && cnArgs.cookieDomain !== '' ? 'domain=' + cnArgs.cookieDomain + ';' : '' ) + ( cnArgs.cookiePath !== undefined && cnArgs.cookiePath !== '' ? 'path=' + cnArgs.cookiePath + ';' : '' );

		// trigger custom event
		$.event.trigger( {
			type: 'setCookieNotice',
			value: cookie_value,
			time: cnTime,
			expires: cnLater
		} );

		// hide message container
		if ( cnArgs.hideEffect === 'fade' ) {
			cnDomNode.fadeOut( 300, function () {
				cnSelf.removeCookieNotice();
			} );
		} else if ( cnArgs.hideEffect === 'slide' ) {
			cnDomNode.slideUp( 300, function () {
				cnSelf.removeCookieNotice();
			} );
		} else {
			cnSelf.removeCookieNotice();
		}

		if ( cookie_value && cnArgs.redirection === '1' ) {
			var url = window.location.protocol + '//',
				hostname = window.location.host + '/' + window.location.pathname;

			if ( cnArgs.cache === '1' ) {
				url = url + hostname.replace( '//', '/' ) + ( window.location.search === '' ? '?' : window.location.search + '&' ) + 'cn-reloaded=1' + window.location.hash;

				window.location.href = url;
			} else {
				url = url + hostname.replace( '//', '/' ) + window.location.search + window.location.hash;

				window.location.reload( true );
			}

			return;
		}
	};

	// remove Cookie Notice
	$.fn.removeCookieNotice = function ( cookie_value ) {
		$( '#cookie-notice' ).remove();
		$( 'body' ).removeClass( 'cookies-not-accepted' );
	};

	$( document ).ready( function () {
		var cnDomNode = $( '#cookie-notice' );

		// handle on scroll
		if ( cnArgs.onScroll === 'yes' ) {
			cnHandleScroll = function () {
				var win = $( this );

				if ( win.scrollTop() > parseInt( cnArgs.onScrollOffset ) ) {
					// accept cookie
					win.setCookieNotice( 'accept' );

					// remove itself after cookie accept
					win.off( 'scroll', cnHandleScroll );
				}
			};
		}

		// handle set-cookie button click
		$( document ).on( 'click', '.cn-set-cookie', function ( e ) {
			e.preventDefault();

			$( this ).setCookieNotice( $( this ).data( 'cookie-set' ) );
		} );

		// display cookie notice
		if ( document.cookie.indexOf( 'cookie_notice_accepted' ) === -1 ) {
			// handle on scroll
			if ( cnArgs.onScroll === 'yes' ) {
				$( window ).on( 'scroll', cnHandleScroll );
			}

			if ( cnArgs.hideEffect === 'fade' ) {
				cnDomNode.fadeIn( 300 );
			} else if ( cnArgs.hideEffect === 'slide' ) {
				cnDomNode.slideDown( 300 );
			} else {
				cnDomNode.show();
			}

			$( 'body' ).addClass( 'cookies-not-accepted' );
		} else {
			cnDomNode.removeCookieNotice();
		}
	} );

} )( jQuery );