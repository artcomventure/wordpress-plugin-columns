(function ( $, undefined ) {

    tinymce.PluginManager.add( 'columns', function ( editor ) {
        tinymce.PluginManager.requireLangPack( 'columns', 'de_DE' );

        editor.addButton( 'columns', {
            type: 'panelbutton',
            tooltip: editor.getLang( 'columns.Columns' ),
            icon: 'columns',
            panel: {
                role: 'application',
                html: renderColumnsPanel,

                onclick: function ( e ) {
                    var $columnSet = $( editor.dom.getParent( editor.selection.getNode(), '.columns' ) ),
                        aColumns = $( 'div.column', $columnSet ), iColumns,
                        content = [], sInsert = '', i,
                        isLiquid = $( '#liquid-text' ).is( ':checked' ),

                    // current cursor range
                        range = editor.selection.getRng();

                    // on replace or refresh in case of some empty not text-node
                    if ( $columnSet.length && range.startContainer.nodeType == 3 && !range.startContainer.textContent.trim() ) {
                        // get column
                        var column = editor.dom.getParent( range.startContainer.parentNode, '.column' );

                        // clear column
                        while ( column.firstChild ) {
                            column.removeChild( column.firstChild );
                        }

                        // insert paragraph
                        $( column ).append( '<p>' + editor.getLang( 'columns.Column' ) + ' ' + ( aColumns.index( column ) + 1 ) + '</p>' );

                        // re-set range start
                        range.setStart( column.firstChild, 1 );
                    }

                    // get number of columns
                    if ( $.isNumeric( e ) || e == 'narrow-wide' || e == 'wide-narrow' ) {
                        iColumns = e;

                        if ( column == undefined ) return;
                    }
                    else {
                        iColumns = $( e.target ).closest( 'td' ).data( 'columns' );
                        this.hide();
                    }

                    // save for re-set range in editor
                    var rangeStartOffset = range.startOffset,
                        rangeEndOffset = range.endOffset;

                    // replace or refresh
                    if ( $columnSet.length ) {
                        if ( aColumns.length ) {
                            // insert text node (range) marker for re-set the cursor later
                            range.startContainer.nodeValue = '[range-start-container]' + range.startContainer.nodeValue;
                            range.endContainer.nodeValue += '[range-end-container]';

                            // collect content
                            for ( i = 0; i < aColumns.length; i++ ) {
                                content.push( aColumns[i].innerHTML.trim() );
                            }
                        }
                        // is liquid
                        else content = [$columnSet[0].innerHTML.trim()];
                    }
                    // insert
                    else {
                        content = [tinyMCE.activeEditor.selection.getContent()];
                        if ( content[0] ) content[0] = '<p>' + content[0] + '</p>';
                    }

                    if ( iColumns == undefined ) e.stopPropagation();
                    else {
                        // remove
                        if ( iColumns === 1 ) sInsert = content.join( '' );
                        // columns
                        else {
                            // wide-narrow-wide
                            if ( !$.isNumeric( iColumns ) ) {
                                var widths = iColumns.split( '-' );
                                iColumns = widths.length;
                            }

                            sInsert = '<div class="columns columns-' + iColumns
                            + ( isLiquid ? ' columns-liquid' : '' )
                            + '" data-columns="' + iColumns + '">';

                            for ( i = 0; i < iColumns; i++ ) {
                                // for more 'content' then columns
                                // put rest in last column
                                if ( i == iColumns - 1 ) content[i] = content.slice( i ).join( '' );

                                if ( !isLiquid ) {
                                    sInsert += '<div class="column column-' + (i + 1) + ( widths ? ' column-' + widths[i] : '' ) + '">';
                                }

                                sInsert += ( content[i] || '<p>' + editor.getLang( 'columns.Column' ) + ' ' + (i + 1) + '</p>' );

                                if ( !isLiquid ) sInsert += '</div>'; // column
                            }

                            sInsert += '</div>'; // columns
                        }

                        var $insert = $( sInsert ),
                            startContainer = null,
                            endContainer = null;

                        // replace/remove
                        if ( $columnSet.length ) {
                            $columnSet.replaceWith( $insert );

                            // remove range marker
                            function findRangeContainers( node ) {
                                if ( !node ) return;

                                node = node.firstChild;

                                while ( node && ( !startContainer || !endContainer ) ) {
                                    // is text node
                                    if ( node.nodeType == 3 ) {
                                        if ( node.nodeValue.match( new RegExp( '^\\[range-start-container\\]' ) ) ) {
                                            node.nodeValue = node.nodeValue.replace( '[range-start-container]', '' );
                                            startContainer = node;
                                        }

                                        if ( node.nodeValue.match( new RegExp( '\\[range-end-container\\]$' ) ) ) {
                                            node.nodeValue = node.nodeValue.replace( '[range-end-container]', '' );
                                            endContainer = node;
                                        }
                                    }
                                    else if ( node.nodeType == 1 ) findRangeContainers( node );

                                    node = node.nextSibling;
                                }
                            }

                            $insert.each( function () {
                                findRangeContainers( this );
                            } );

                            // re-set range marker
                            if ( startContainer && endContainer ) {
                                range.setStart( startContainer, rangeStartOffset );
                                range.setEnd( endContainer, rangeEndOffset );

                                editor.selection.setRng( range );
                            }
                        }
                        // insert
                        else editor.insertContent( sInsert );
                    }
                }
            },

            onPostRender: function () {
                // ...
                editor.on( 'init', function () {
                    // editor body
                    var body = editor.dom.getParent( editor.selection.getNode(), 'body' );

                    // add/remove paragraph button
                    $( body ).on( 'mouseenter mouseleave', 'div.columns', function ( e ) {
                        // get column set
                        var $columnset = $( e.target );
                        if ( !$columnset.is( '.columns' ) ) $columnset = $columnset.closest( 'div.columns' );

                        // paragraph buttons ~
                        // ~ remove
                        if ( e.type == 'mouseleave' ) $columnset.find( 'span.add-paragraph' ).remove();
                        // ~ add
                        else {
                            var $addParagraph = $( '<span class="add-paragraph" />' );
                            $addParagraph.attr( 'title', editor.getLang( 'columns.AddParagraph' ) )
                                .on( 'click', function () {
                                    // define position where to add paragraph
                                    var where = 'after';
                                    if ( $( this ).is( ':first-child' ) ) where = 'before';

                                    // eventually add empty paragraph
                                    $columnset[where]( '<p>&nbsp;</p>' );
                                } );

                            // eventually add to html
                            $columnset.prepend( $addParagraph ).append( $addParagraph.clone( true ) );
                        }
                    } );
                } );

                var columnsButton = this,
                    events = ['nodechange', 'click', 'show'];

                for ( var i = 0; i < events.length; i++ ) {
                    editor.on( events[i], function ( e ) {
                        // editor body
                        var body = editor.dom.getParent( editor.selection.getNode(), 'body' );

                        // reset
                        $( 'div.columns', $( body ) ).removeClass( 'active' );

                        // get current columnset
                        var columns = editor.dom.getParent( editor.selection.getNode(), '.columns' );
                        columnsButton.active( !!columns );

                        // insert
                        if ( !columns ) return;

                        var $columns = $( columns );

                        $columns.addClass( 'active' );

                        // refresh (on every columns click)
                        if ( e.type == 'click' ) {
                            var $firstColumn = $columns.find( 'div.column:first-of-type' );

                            // get current columns set
                            if ( $firstColumn.hasClass( 'column-wide' ) ) columns = 'wide-narrow';
                            else if ( $firstColumn.hasClass( 'column-narrow' ) ) columns = 'narrow-wide';
                            else columns = $columns.data( 'columns' );

                            // trigger
                            editor.buttons.columns.panel.onclick( columns );
                        }
                    } );
                }
            }
        } );

        /**
         * Columns panel markup.
         *
         * @returns {string}
         */
        function renderColumnsPanel() {
            var html = '<table class="mce-grid mce-grid-border mce-columns-grid"><tbody>';

            // liquid text
            html += '<tr style="display:none;"><td colspan="10">';
            html += '<input id="liquid-text" type="checkbox" />';
            html += '<label for="liquid-text">Liquid Text</label>';
            html += '</td></tr>';

            // free select
            html += '<tr>';
            for ( var i = 1; i < 10; i++ ) {
                html += '<td data-columns="' + i + '">';
                html += '<span>' + ( i > 1 ? i : '&times;' ) + '</span>';
                html += '</td>';
            }
            html += '</tr>';

            // narrow - wide
            html += '<tr><td data-columns="narrow-wide" colspan="3">';
            html += '<span>1/3</span>';
            html += '</td><td data-columns="narrow-wide" colspan="6">';
            html += '<span>2/3</span>';
            html += '</td></tr>';

            // narrow - wide
            html += '<tr><td data-columns="wide-narrow" colspan="6">';
            html += '<span>2/3</span>';
            html += '</td><td data-columns="wide-narrow" colspan="3">';
            html += '<span>1/3</span>';
            html += '</td></tr>';

            html += '</tbody></table>';

            return html
        }
    } );

})( jQuery );
