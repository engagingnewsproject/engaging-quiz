var gulp = require('gulp');
var runSequence = require('run-sequence');
var browserSync = require('browser-sync');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var concat = require("gulp-concat");
var insert = require('gulp-insert');
const plumber = require('gulp-plumber');
var reload  = browserSync.reload;


// Static Server + watching css/html files
gulp.task('serve', ['cssQuizCreate', 'cssQuizTake', 'quizCreateJS', 'quizDashboardJS', 'quizResultsJS', 'quizTakeJS', 'quizTakeIframeParentJS', 'quizTakeUtilityJS'], function() {

    browserSync({
        proxy: "localhost:10121",
    });

    // quiz create – :root + parts/*.css
    gulp.watch(['public/quiz-create/css/enp_quiz-create.css', 'public/quiz-create/css/parts/*.css'], ['cssQuizCreate']);
    // watch javascript files
    gulp.watch('public/quiz-create/js/quiz-create/*.js', ['quizCreateJS']);
    gulp.watch('public/quiz-create/js/dashboard.js', ['quizDashboardJS']);
    gulp.watch('public/quiz-create/js/quiz-results/*.js', ['quizResultsJS']);

    // quiz take – :root + parts/*.css
    gulp.watch(['public/quiz-take/css/enp_quiz-take.css', 'public/quiz-take/css/parts/*.css'], ['cssQuizTake']);

    // compress on change
    gulp.watch('public/quiz-take/js/quiz-take/*.js', ['quizTakeJS']);

    // compress on change
    gulp.watch('public/quiz-take/js/iframe-parent/*.js', ['quizTakeIframeParentJS']);

    // compress on change Quiz Take utilities
    gulp.watch('public/quiz-take/js/utilities/*.js', ['quizTakeUtilityJS']);

    // Watch for file changes
    onChangeReload = ["public/quiz-create/css/enp_quiz-create.min.css",
                    "public/quiz-create/templates/*.php",
                    "public/quiz-create/includes/*.php",
                    "public/quiz-create/js/dist/quiz-create.min.js",
                    "public/quiz-take/css/enp_quiz-take.min.css",
                    "public/quiz-create/templates/partials/*.php",
                    "public/quiz-take/*.php",
                    "public/quiz-take/includes/*.php",
                    "public/quiz-take/js/dist/quiz-take.min.js"
                    ];
    gulp.watch(onChangeReload).on('change', reload);
});

/* Build CSS: concat :root + parts (order matters), then autoprefixer + minify. */
/* Order matches SCSS @import in enp_quiz-create.scss (setup → … → slider). */
var quizCreateCssParts = [
    'public/quiz-create/css/enp_quiz-create.css',
    'public/quiz-create/css/parts/setup.css',
    'public/quiz-create/css/parts/utilities.css',
    'public/quiz-create/css/parts/typography.css',
    'public/quiz-create/css/parts/base.css',
    'public/quiz-create/css/parts/animations.css',
    'public/quiz-create/css/parts/forms.css',
    'public/quiz-create/css/parts/quiz-create.css',
    'public/quiz-create/css/parts/quiz-preview.css',
    'public/quiz-create/css/parts/quiz-publish.css',
    'public/quiz-create/css/parts/ab-create.css',
    'public/quiz-create/css/parts/dashboard.css',
    'public/quiz-create/css/parts/breadcrumbs.css',
    'public/quiz-create/css/parts/quiz-results.css',
    'public/quiz-create/css/parts/ab-results.css',
    'public/quiz-create/css/parts/slider.css'
];
/* Keep original color format (e.g. rgba) so :root variables are not changed to hsla.
 * cssnano 3.x (used by gulp-cssnano) has no "preset" API; pass colormin: false directly. */
var cssnanoSafe = { colormin: false };
gulp.task('cssQuizCreate', function () {
    return gulp.src(quizCreateCssParts, { base: 'public/quiz-create/css' })
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(concat('enp_quiz-create.css'))
        .pipe(autoprefixer())
        .pipe(cssnano(cssnanoSafe))
        .pipe(rename({ suffix: '.min' }))
        .pipe(sourcemaps.write('.', {
            mapSources: function (sourcePath) {
                return sourcePath === 'enp_quiz-create.css' ? sourcePath : 'parts/' + sourcePath;
            }
        }))
        .pipe(gulp.dest('public/quiz-create/css/'));
});

/* Quiz Take CSS: concat :root + parts (order matches enp_quiz-take.scss). */
var quizTakeCssParts = [
    'public/quiz-take/css/enp_quiz-take.css',
    'public/quiz-take/css/parts/setup.css',
    'public/quiz-take/css/parts/utilities.css',
    'public/quiz-take/css/parts/typography.css',
    'public/quiz-take/css/parts/forms.css',
    'public/quiz-take/css/parts/animations.css',
    'public/quiz-take/css/parts/quiz.css',
    'public/quiz-take/css/parts/slider.css'
];
gulp.task('cssQuizTake', function () {
    return gulp.src(quizTakeCssParts, { base: 'public/quiz-take/css' })
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(concat('enp_quiz-take.css'))
        .pipe(autoprefixer())
        .pipe(cssnano(cssnanoSafe))
        .pipe(rename({ suffix: '.min' }))
        .pipe(sourcemaps.write('.', {
            mapSources: function (sourcePath) {
                return sourcePath === 'enp_quiz-take.css' ? sourcePath : 'parts/' + sourcePath;
            }
        }))
        .pipe(gulp.dest('public/quiz-take/css/'));
});

gulp.task('quizCreateJS', function(callback) {
    runSequence('concatQuizCreateJS',
             'compressQuizCreateJS',
             callback);
});

gulp.task('concatQuizCreateJS', function() {
    rootPath = "public/quiz-create/js/quiz-create/";
    src = [rootPath+"quiz-create--utilities.js",
           rootPath+"quiz-create--templates.js",
           rootPath+"quiz-create--onLoad.js",
           rootPath+"quiz-create--ux.js",
           "public/quiz-create/js/utilities/display-messages.js",
           rootPath+"quiz-create--reorder-question.js",
           rootPath+"quiz-create--save-question.js",
           rootPath+"quiz-create--save-question-image.js",
           rootPath+"quiz-create--save-mc-option.js",
           rootPath+"quiz-create--save-slider.js",
           rootPath+"quiz-create--save.js",
           "public/quiz-take/js/quiz-take/quiz-take--slider.js"

        ];
    filename = 'quiz-create';
    dist = 'public/quiz-create/js/dist/';
    return concatjQuery(src, filename, dist);
});

gulp.task('compressQuizCreateJS', function() {
    dist = 'public/quiz-create/js/dist/';
    return compressJS(dist);
});


gulp.task('quizDashboardJS', function(callback) {
    runSequence('concatQuizDashboardJS',
             'compressQuizCreateJS',
             callback);
});

gulp.task('concatQuizDashboardJS', function() {
    rootPath = "public/quiz-create/js/";
    src = [rootPath+"dashboard.js",
           rootPath+"utilities/display-messages.js",
        ];
    filename = 'dashboard';
    dist = 'public/quiz-create/js/dist/';
    return concatjQuery(src, filename, dist);
});

gulp.task('quizResultsJS', function(callback) {
    runSequence('concatQuizResultsJS',
                'concatQuizABResultsJS',
                'compressQuizCreateJS',
             callback);
});

gulp.task('quizABResultsJS', function(callback) {
    runSequence('concatQuizABResultsJS',
            'compressQuizCreateJS',
             callback);
});

gulp.task('concatQuizResultsJS', function() {
    rootPath = "public/quiz-create/js/quiz-results/";
    src = [rootPath+"quiz-results.js",
           rootPath+"quiz-results--init.js",
           rootPath+"quiz-results--slider.js"
        ];
    filename = 'quiz-results';
    dist = 'public/quiz-create/js/dist/';
    return concatjQuery(src, filename, dist);
});

gulp.task('concatQuizABResultsJS', function() {
    rootPath = "public/quiz-create/js/quiz-results/";
    src = [rootPath+"ab-results.js",
           rootPath+"quiz-results--init.js",
           rootPath+"quiz-results--slider.js"
        ];
    filename = 'ab-results';
    dist = 'public/quiz-create/js/dist/';
    return concatjQuery(src, filename, dist);
});


gulp.task('quizTakeJS', function(callback) {
    runSequence('concatQuizTakeJS',
             'compressQuizTakeJS',
             callback);
});

gulp.task('concatQuizTakeJS', function() {
    rootPath = "public/quiz-take/js/quiz-take/";
    src = [//"public/quiz-take/js/utilities/jquery.ui.touch-punch.min.js",
           rootPath+"quiz-take--utilities.js",
           rootPath+"quiz-take--templates.js",
           rootPath+"quiz-take--ux.js",
           rootPath+"quiz-take--postmessage.js",
           rootPath+"quiz-take--question.js",
           rootPath+"quiz-take--question-explanation.js",
           rootPath+"quiz-take--mc-option.js",
           rootPath+"quiz-take--slider.js",
           rootPath+"quiz-take--quiz-end.js",
           rootPath+"quiz-take--quiz-restart.js",
           rootPath+"quiz-take--init.js",
        ];
    filename = 'quiz-take';
    dist = 'public/quiz-take/js/dist/';
    return concatjQuery(src, filename, dist);
});

gulp.task('quizTakeIframeParentJS', function(callback) {
    runSequence('concatQuizTakeIframeParentJS',
             'compressQuizTakeJS',
             callback);
});

gulp.task('concatQuizTakeIframeParentJS', function() {
    rootPath = "public/quiz-take/js/iframe-parent/";
    src = [rootPath+"enpIframeQuiz.js",
           rootPath+"iframe-parent--listener.js",
          ];
    filename = 'iframe-parent';
    dist = 'public/quiz-take/js/dist/';
    return gulp.src(src)
      .pipe(plumber())
      .pipe(concat(filename+'.js'))
      .pipe(gulp.dest(dist));
});

gulp.task('quizTakeUtilityJS', function(callback) {
    runSequence('concatQuizTakeUtilityJS',
             'compressQuizTakeJS',
             callback);
});

gulp.task('concatQuizTakeUtilityJS', function() {
    rootPath = "public/quiz-take/js/utilities/";
    src = [rootPath+"html5shiv.min.js",
           rootPath+"jquery-ui.min.js",
           rootPath+"underscore.min.js",
           rootPath+"jquery.ui.touch-punch.min.js"
        ];
    filename = 'utilities';
    dist = 'public/quiz-take/js/dist/';
    return gulp.src(src)
      .pipe(plumber())
      .pipe(concat(filename+'.js'))
      .pipe(gulp.dest(dist));
});

gulp.task('compressQuizTakeJS', function() {
    dist = 'public/quiz-take/js/dist/';
    return compressJS(dist);
});

function concatjQuery(src, filename, dist) {
    return gulp.src(src)
      .pipe(plumber())
      .pipe(concat(filename+'.js'))
      .pipe(insert.wrap('jQuery( document ).ready( function( $ ) {', '});'))
      .pipe(gulp.dest(dist));
}

function compressJS(path) {
    return gulp.src([path+"*.js","!"+path+"*.min.js"])
      .pipe(plumber())
      .pipe(uglify())
      .pipe(rename({
        suffix: '.min'
      }))
      .pipe(gulp.dest(path));
}

// Creating a default task
gulp.task('default', ['serve']);
