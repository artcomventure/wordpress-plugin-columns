var gulp = require( 'gulp' ),

    // gulp plugins

    sass = require( 'gulp-sass' ),
    // css vendor prefixes
    autoprefixer = require( 'gulp-autoprefixer' ),
    // beautify css
    csscomb = require( 'gulp-csscomb' ),
    replace = require( 'gulp-replace' ),
    // deletion
    del = require( 'del' ),
    // concat files
    concat = require( 'gulp-concat' ),

    // doesn't break pipe on error
    // so we don't need to restart gulp
    plumber = require( 'gulp-plumber' ),
    // get notification on error
    notify = require( 'gulp-notify' ),
    onError = function( error ) {
        notify.onError( {
            title:    'Gulp Failure :/',
            message:  '<%= error.message %>',
            sound:    'Beep'
        } )( error );

        this.emit( 'end' );
    },

    // all our scss files
    scssFiles = [
        'css/**/*.scss'
    ];

/**
 * Compile scss to css and create sourcemap.
 */
gulp.task( 'scss', function() {
    return gulp.src( scssFiles, { base: 'wp-content' } )
        .pipe( plumber( { errorHandler: onError } ) )
        // scss to css
        .pipe( sass() )
        // vendor prefixes
        .pipe( autoprefixer( {
            browsers: ['last 3 versions']
        } ) )
        // beautify css
        .pipe( csscomb() )
        // in addition to csscomb (didn't found any options for this)
        // ... add a blank line between two instructions
        .pipe( replace( /}\n(\.|#|\w|\s*\d)/g, "}\n\n$1" ) )
        // ... remove blank lines in instruction
        .pipe( replace( /;\s*\n(\s*\n)+/g, ";\n" ) )
        .pipe( gulp.dest( 'wp-content' ) );
} );

/**
 * Clear build/ folder.
 */
gulp.task( 'clear:build', function() {
    del.sync( 'build/**/*' );
} );

gulp.task( 'build', ['clear:build', 'scss'], function() {
    // collect all needed files
    gulp.src( [
        '**/*',
        // ... but:
        '!**/*.scss',
        '!**/*.css', // will be collected see next function
        '!*.md',
        '!LICENSE',
        '!readme.txt',
        '!gulpfile.js',
        '!package.json',
        '!.csscomb.json',
        '!.gitignore',
        '!node_modules{,/**}',
        '!build{,/**}',
        '!assets{,/**}'
    ] ).pipe( gulp.dest( 'build/' ) );

    // collect css files
    gulp.src( [ '**/*.css', '!css/columns.css', '!css/columns.min.css' ,'!node_modules{,/**}' ] )
        .pipe( gulp.dest( 'build/' ) );

    // concat files for WP's readme.txt
    // manually validate output with https://wordpress.org/plugins/about/validator/
    gulp.src( [ 'readme.txt', 'README.md', 'CHANGELOG.md' ] )
        .pipe( concat( 'readme.txt' ) )
        // remove screenshots
        // todo: scrennshot section for WP's readme.txt
        .pipe( replace( /\n\!\[image\]\([^)]+\)\n/g, '' ) )
        // WP markup
        .pipe( replace( /#\s*(Changelog)/g, "## $1" ) )
        .pipe( replace( /###\s*([^(\n)]+)/g, "=== $1 ===" ) )
        .pipe( replace( /##\s*([^(\n)]+)/g, "== $1 ==" ) )
        .pipe( replace( /==\s(Unreleased|[0-9\s\.-]+)\s==/g, "= $1 =" ) )
        .pipe( replace( /#\s*[^\n]+/g, "== Description ==" ) )
        .pipe( gulp.dest( 'build/' ) );
} );

/**
 * Watch tasks.
 *
 * Init watches by calling 'gulp' in terminal.
 */
gulp.task( 'default', ['scss'], function() {
    gulp.watch( scssFiles, ['scss'] );
} );
