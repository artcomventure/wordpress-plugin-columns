( function( $, window, undefined ) {

    var $form = $( '#columns-options-form');
    if ( !$form.length ) return;

    $( 'input[name="columns[columns]"]', $form).on( 'focus keydown keyup blur', function() {
        var $this = $( this );

        $this.val( $this.val().slice( 0, 1 ).replace( new RegExp( '[^2-9]' ), '') );
    } );

    var $breakpoints = $( 'input[name="columns[tablet]"], input[name="columns[mobile]"]', $form)
        // only integer values
        .on( 'focus keydown keyup blur', function() {
            var $this = $( this );

            $this.val( $this.val().replace( new RegExp( '[^\\d]', 'g' ), '').replace( new RegExp( '^0+' ), '' ) );
        } );

    $( 'input[name="columns[responsive]"]', $form).on( 'change', function() {
        if ( $( this).is( ':checked' ) ) $breakpoints.prop( 'readonly', false );
        else $breakpoints.prop( 'readonly', true );
    }).trigger( 'change' );

} )( jQuery, this, this.document );
