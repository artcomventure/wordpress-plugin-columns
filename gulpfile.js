var gulp = require( 'gulp' ),

    // gulp plugins

    sass = require( 'gulp-sass' ),
    sourcemaps = require( 'gulp-sourcemaps' ),
    // css vendor prefixes
    autoprefixer = require( 'gulp-autoprefixer' ),
    rename = require( 'gulp-rename' ),
    // minimize css
    cssnano = require( 'gulp-cssnano' ),
    // uglify (and minimize) js
    uglify = require( 'gulp-uglify' ),
    // beautify css
    csscomb = require( 'gulp-csscomb' ),
    replace = require( 'gulp-replace' ),
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
    ],

    // all our css files
    cssFiles = [
        'css/**/*.css',
        // ... but already minimized ones
        '!**/*.min.css'
    ],

    // all our js files
    jsFiles = [
        'js/**/*.js',
        // ... but already minimized ones
        '!**/*.min.js'
    ];

/**
 * Compile scss to css and create sourcemap.
 */
gulp.task( 'scss', function() {
    return gulp.src( scssFiles, { base: 'wp-content' } )
        .pipe( plumber( { errorHandler: onError } ) )
        .pipe( sourcemaps.init() )
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
        // write sourcemap
        .pipe( sourcemaps.write( '.' ) )
        .pipe( gulp.dest( 'wp-content' ) );
} );

/**
 * Compress css files.
 */
gulp.task( 'css', ['scss'], function() {
    return gulp.src( cssFiles, { base: 'wp-content' } )
        .pipe( plumber( { errorHandler: onError } ) )
        // rename to FILENAME.min.css
        .pipe( rename( { suffix: '.min' } ) )
        // minimize css
        .pipe( cssnano() )
        .pipe( gulp.dest( 'wp-content' ) );
} );

/**
 * Compress and uglify js files.
 */
gulp.task( 'js', function() {
    return gulp.src( jsFiles, { base: 'wp-content' } )
        .pipe( plumber( { errorHandler: onError } ) )
        // rename to FILENAME.min.js
        .pipe( rename( { suffix: '.min' } ) )
        // uglify and compress
        .pipe( uglify() )
        .pipe( gulp.dest( 'wp-content' ) );
} );

/**
 * Create build.
 */
gulp.task( 'build', ['css', 'js'], function() {
    // collect all needed files
    gulp.src( [
        '**/*',
        // ... but:
        '!**/*.scss',
        '!*.md',
        '!LICENSE',
        '!readme.txt',
        '!gulpfile.js',
        '!package.json',
        '!.gitignore',
        '!node_modules{,/**}',
        '!build{,/**}'
    ] ).pipe( gulp.dest( 'build/' ) );

    // concat files for WP's readme.txt
    // manually validate output with https://wordpress.org/plugins/about/validator/
    gulp.src( [ 'readme.txt', 'README.md', 'CHANGELOG.md' ] )
        .pipe( concat( 'readme.txt' ) )
        // WP markup
        .pipe( replace( /#\s*(Changelog)/g, "## $1" ) )
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
gulp.task( 'default', function() {
    gulp.watch( scssFiles, ['css'] );
    gulp.watch( jsFiles, ['js'] );
} );
