( function( $, undefined ) {

    var $body = $( 'body.plugins-php' );
    if ( !$body.length ) return;

    var $columns = $( '#columns' );
    if ( !$columns.length ) return;

    var $version = $( 'div.plugin-version-author-uri', $columns );
    $( 'a:last-child', $version ).removeClass( 'thickbox' )
        .attr( 'href', columnsData.URI )
        .attr( 'target', '_blank' );

    // check for repo use
    $.get( columnsData.pluginDir + '/.git/config' )
        .done( updateMessage ).fail( updateMessage );

    function updateMessage( data ) {
        var $update_message = $( '#columns-update').find( 'div.update-message' );

        // done: git in use
        if ( data.status == undefined ) {
            $update_message.html( columnsT9n.update_message );
        }
        // fail: download files
        else {
            $( 'a', $update_message).each( function( i ) {
                var $link = $( this ).attr( 'class', '' );

                // update link
                if ( i ) $link.replaceWith( $link.clone().attr( 'href', columnsData.masterZip ) );
                // detail link
                else $link.attr( 'href', columnsData.gitChangelog).attr( 'target', '_blank' );
            } );
        }
    }

} )( jQuery );