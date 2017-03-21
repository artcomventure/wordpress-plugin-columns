(function ( $, window ) {

    var $form = $( '#columns-options-form' );
    if ( !$form.length ) return;

    // validate columns
    var $columns = $( 'input[name="columns[columns]"]', $form ).on( 'focus keydown keyup blur', function () {
        $columns.val( $columns.val().slice( 0, 1 ).replace( /[^2-9]/, '' ) );
    } );

    // validate @media breakpoints
    var $breakpoints = $( 'input[name="columns[tablet]"], input[name="columns[mobile]"]', $form )
        // only integer values
        .on( 'focus keydown keyup blur', function () {
            var $this = $( this );

            $this.val( $this.val().replace( /[^\d]/g, '' ).replace( new RegExp( '^0+' ), '' ) );
        } );

    // en-/disable @media breakpoint inputs
    $( 'input[name="columns[responsive]"]', $form ).on( 'change', function () {
        if ( $( this ).is( ':checked' ) ) $breakpoints.prop( 'readonly', false );
        else $breakpoints.prop( 'readonly', true );
    } ).trigger( 'change' );

    // validate gap
    var regexp = /\d+(\.\d+)?(px|em|rem|%)/,
        $gap = $( 'input[name="columns[gap]"]', $form ).on( 'focus keydown keyup blur', function () {
            $gap.val( $gap.val().replace( /\s+/g, '' ) );
        } );

})( jQuery, this, this.document );
