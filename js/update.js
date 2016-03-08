( function( $, undefined ) {

    $( document ).ready( function() {

        var $body = $( 'body.plugins-php' );
        if ( !$body.length ) return;

        var $columns = $( '#' + columnsData.id );
        if ( !$columns.length ) return;

        var $version = $( 'div.plugin-version-author-uri', $columns );
        $( 'a:last-child', $version ).removeClass( 'thickbox' )
            .attr( 'href', columnsData.URI )
            .attr( 'target', '_blank' );

        // check for repo use
        $.get( columnsData.pluginDir + '.git/config' )
            .done( updateMessage ).fail( updateMessage );

        function updateMessage( data ) {
            var $update_message = $( 'tr.plugin-update-tr[data-plugin="' + columnsData.pluginFile + '"]').find( 'div.update-message' );

            // done: git in use
            if ( data.status == undefined ) {
                $update_message.html( columnsT9n.update_message );
                //$( 'pre', $update_message).css( 'margin-bottom', 0 );
            }
            // fail: download files
            else {
                $( 'a', $update_message).each( function( i ) {
                    var $link = $( this ).attr( 'class', '' );

                    // detail link
                    if ( !i ) $link.attr( 'href', columnsData.gitChangelog).attr( 'target', '_blank' );
                } );
            }
        }

    } );

} )( jQuery );