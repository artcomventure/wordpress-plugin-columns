( function( $, undefined ) {

    var $body = $( 'body.plugins-php' );
    if ( !$body.length ) return;

    var $columns = $( '#columns.update' );
    if ( !$columns.length ) return;

    var $version = $( 'div.plugin-version-author-uri', $columns );
    $version.html( $version.html().split( '|' ).slice(0,-1).join( '|' ) );

    var $update_message = $( '#columns-update' );
    $( 'div.update-message a', $update_message).each( function() {
        var $link = $( this).off( 'click' );

        if ( $link.hasClass( 'update-link' ) )
            $link.replaceWith( $link.clone().removeClass( 'update-link' ).attr( 'href', 'https://github.com/artcomventure/wordpress-plugin-columns/archive/master.zip' ) );
        else $link.removeClass( 'thickbox' ).attr( 'href', 'https://github.com/artcomventure/wordpress-plugin-columns/blob/master/CHANGELOG.md').attr( 'target', '_blank' );
    } );

} )( jQuery );