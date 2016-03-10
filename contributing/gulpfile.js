var gulp = require('gulp');
var browserSync = require('browser-sync');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var minifyCss = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");

var reload  = browserSync.reload;


// Static Server + watching scss/html files
gulp.task('serve', ['sass', 'compress'], function() {

    browserSync({
        proxy: "dev/quiz"
    });

    gulp.watch('../enp-quiz/public/quiz-create/css/sass/*.{scss,sass}', ['sass']);
    // Watch CSS file for change, but DON'T watch ie.css so our browser doesn't reload twice
    gulp.watch(["../enp-quiz/public/quiz-create/css/enp_quiz-create.min.css", "!../enp-quiz/public/quiz-create/ie.css"]).on('change', reload);
    gulp.watch("../enp-quiz/public/quiz-create/*.php").on('change', reload);
    gulp.watch("../enp-quiz/public/quiz-create/includes/*.php").on('change', reload);
    // watch javascript files
    // compress on change
    gulp.watch('../enp-quiz/public/quiz-create/js/*.js', ['compress']);
    // reload after min file is finished compressing
    gulp.watch("../enp-quiz/public/quiz-create/js/enp_quiz-create.min.js").on('change', reload);
});

gulp.task('sass', function () {
  // gulp.src locates the source files for the process.
  // This globbing function tells gulp to use all files
  // ending with .scss or .sass within the scss folder.
  return gulp.src('../enp-quiz/public/quiz-create/css/sass/*.{scss,sass}')
    // Initializes sourcemaps
    // Uncomment this and the other sourcemaps line to add sourcemaps back in
    //////////// .pipe(sourcemaps.init())

    // Converts Sass into CSS with Gulp Sass
    .pipe(sass({
      errLogToConsole: true
    }))
    // adds prefixes to whatever needs to get done
    .pipe(autoprefixer())

    // minify the CSS
    .pipe(minifyCss())

    // Writes sourcemaps into the CSS file
    // Uncomment this and the other sourcemaps line to add sourcemaps back in
    ///////////// .pipe(sourcemaps.write())

    // rename to add .min
    .pipe(rename({
      suffix: '.min'
    }))
    // Outputs CSS files in the css folder
    .pipe(gulp.dest('../enp-quiz/public/quiz-create/css/'));
});

// minify js
gulp.task('compress', function() {
  return gulp.src(["../enp-quiz/public/quiz-create/js/*.js","!../enp-quiz/public/quiz-create/js/*.min.js"])
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('../enp-quiz/public/quiz-create/js/'));
});


// Creating a default task
gulp.task('default', ['serve']);
