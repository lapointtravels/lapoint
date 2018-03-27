const gulp          = require( 'gulp' )
const sass          = require( 'gulp-sass' )
const sassGlob      = require( 'gulp-sass-glob' )
const autoprefixer  = require( 'gulp-autoprefixer' )
const cleanCSS      = require( 'gulp-clean-css' )
const watch      		= require( 'gulp-watch' )
const sourcemaps    = require( 'gulp-sourcemaps' )
const notify        = require( 'gulp-notify' )
const browserSync   = require( 'browser-sync' ).create()

const gulpif        = require('gulp-if');




gulp.task('browsersync', () => {
  browserSync.init({
    open: false,
    proxy: 'http://lapoint.test',
    ui: {
      port: 8888
    }
  })
})

gulp.task('sass', () => {

  var styles = [
    { input: 'wp-content/themes/lapoint2016/scss/style.scss', output: 'wp-content/themes/lapoint2016'  },
    { input: 'wp-content/themes/lapoint2016/scss/booking.scss', output: './'  },
    { input: 'wp-content/themes/lapoint2016/scss/admin.scss', output: 'wp-content/themes/lapoint2016'  },
    { input: 'wp-content/themes/lapoint2016/scss/admin-editor-style.scss', output: 'wp-content/themes/lapoint2016'  }
  ]

  var ret = styles.map( (el, i, arr) => {
    return gulp.src( el.input )
      .pipe(sourcemaps.init())
      .pipe(sassGlob())
      //.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError).on('error', notify.onError("Ooops")))
      .pipe(sass().on('error', sass.logError).on('error', notify.onError("Ooops")))
      .pipe(autoprefixer())
      //.pipe(cleanCSS())
      .pipe(sourcemaps.write())
      .pipe(gulp.dest(el.output))
      .pipe( gulpif( arr.length - 1 == i, browserSync.stream()) ) // stream if it's the last processed sheet
  })

  /*

  gulp.src('wp-content/themes/lapoint2016/scss/style.scss')
    .pipe(sourcemaps.init())
    .pipe(sassGlob())
    //.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError).on('error', notify.onError("Ooops")))
    .pipe(sass().on('error', sass.logError).on('error', notify.onError("Ooops")))
    .pipe(autoprefixer())
    //.pipe(cleanCSS())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('wp-content/themes/lapoint2016'))


  gulp.src('wp-content/themes/lapoint2016/scss/booking.scss')
    .pipe(sourcemaps.init())
    .pipe(sassGlob())
    //.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError).on('error', notify.onError("Ooops")))
    .pipe(sass().on('error', sass.logError).on('error', notify.onError("Ooops")))
    .pipe(autoprefixer())
    //.pipe(cleanCSS())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./'))
    .pipe(browserSync.stream())

    */

})


// make sure things are automatically built when changes are made to the source files
gulp.task('watchsass', () => {

  watch( 'wp-content/themes/lapoint2016/scss/**/*.scss', () => {
    gulp.start( 'sass' )
  })

})


gulp.task('dev', ['watchsass', 'browsersync'])