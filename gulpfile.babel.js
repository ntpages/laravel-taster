const { src, dest } = require('gulp');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

exports.default = function () {
    return src('./resources/taster.js')
        .pipe(babel({ presets: ['@babel/preset-env'] }))
        .pipe(uglify())
        .pipe(dest('./public/js'))
}
