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

        // update update message
        function updateMessage( data ) {
            $( 'tr.plugin-update-tr[data-plugin="' + columnsData.pluginFile + '"]')
                .find( 'div.update-message' )
                .html( columnsT9n['update_message_' + ( !!data.status ? 'files' : 'git' )] );
        }

    } );

} )( jQuery );