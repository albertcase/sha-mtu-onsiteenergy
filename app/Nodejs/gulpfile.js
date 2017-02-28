var module_path = '../../../gonodejs/node_modules/';

var gulp = require('gulp');
var uglify = require(module_path+'gulp-uglify');
var pump = require(module_path+'pump');
var rename = require(module_path+"gulp-rename");
var minifyCSS = require(module_path+'gulp-minify-css');

var fjs = [
  '../../src/Wechat/ApiBundle/Resources/public/js'
];

var fcss = [
  '../../src/Wechat/ApiBundle/Resources/public/css'
];

gulp.task('compress_js', function (cb) {
  fjs.forEach(function(path){
    pump([
          gulp.src([path+'/*!(.min).js', '!'+path+'/*.min.js']),
          uglify(),
          rename({suffix: '.min'}),
          gulp.dest(path)
      ],
      cb
    );
  });
});

gulp.task('compress_css', function (cb) {
  fcss.forEach(function(path){
    pump([
          gulp.src([path+'/*.css', '!'+path+'/*.min.css']),
          minifyCSS(),
          rename({suffix: '.min'}),
          gulp.dest(path)
      ],
      cb
    );
  });
});
