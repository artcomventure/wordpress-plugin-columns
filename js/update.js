( function( $, undefined ) {

    $( document ).ready( function() {

        var $body = $( 'body.plugins-php' );
        if ( !$body.length ) return;

        $( 'tr.plugin-update-tr[data-plugin="' + columnsData.pluginFile + '"]')
            .find( 'a.thickbox').removeClass( 'thickbox')
            .attr( 'href', columnsData.gitURI );

    } );

} )( jQuery );