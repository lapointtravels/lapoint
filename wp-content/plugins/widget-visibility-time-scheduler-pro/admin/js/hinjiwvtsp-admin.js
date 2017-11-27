jQuery( document ).ready( function( $ ){
	/*
	 * toggle visibility of scheduler in widget
	 */
	$( document ).on( 'click', '.wvtsp-link', function( e ) {
		// remove default behaviour
        e.preventDefault();
		// get grandparent element of clicked link
		var origin_parent = $( this ).parent().parent();
		// if scheduler is closed: open it, else: close it
		// (i.e. change the css class name to let the 'display' property change from 'block' to 'none' and vice versa)
		// and change the text of the clicked link
		if ( origin_parent.hasClass( 'wvtsp-collapsed' ) ) {
			origin_parent.removeClass( 'wvtsp-collapsed' ).addClass( 'wvtsp-expanded' );
			$( this ).text( wvtsp_i18n.close_scheduler )
		} else {
			origin_parent.removeClass( 'wvtsp-expanded' ).addClass( 'wvtsp-collapsed' );
			$( this ).text( wvtsp_i18n.open_scheduler )
		}
    } );
} );
