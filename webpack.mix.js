const mix = require('laravel-mix')

const AssetsPlugin = require('assets-webpack-plugin')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .version()

/*
 |--------------------------------------------------------------------------
 | Plugins
 |--------------------------------------------------------------------------
 */

const LiveReloadPlugin = require('webpack-livereload-plugin')

mix.webpackConfig({
    plugins: [new LiveReloadPlugin(), new AssetsPlugin()],

    output: {
        chunkFilename: 'js/chunks/[chunkhash].js',
    },
})
