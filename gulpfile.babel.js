const { src, dest } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

exports.default = function () {
    return src([
        './resources/polyfills/*.js',
        './resources/index.js'
    ])
        .pipe(concat('index.js'))
        .pipe(babel({ presets: ['@babel/preset-env'] }))
        .pipe(uglify())
        .pipe(dest('./public/js'))
}
