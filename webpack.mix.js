let path = require('path');
const mix = require('laravel-mix');
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')

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

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vue({
        version:2,
        extractStyles: false,
        globalStyles: {
            css: ['public/css/app.css'],
        },
    })
    .webpackConfig({
        resolve: {
            extensions: ['.js', '.json', '.vue', '.ts'],
            alias: {
                '~': path.join(__dirname, 'resources/js'),
                'resources': path.join(__dirname, 'resources'),
            },
        },
        module: {
            rules: [
                {
                    test: /\.ts?$/,
                    use: [{
                        loader: "ts-loader",
                        options: { appendTsSuffixTo: [/\.vue$/] }
                    }]
                }
            ]
        }
    })
    .browserSync({
        proxy: 'ica:8000',
        port: 3000,
        open: false,
    })
    .extend('vuetify', new class {
        webpackConfig (config) {
            config.plugins.push(new VuetifyLoaderPlugin())
        }
    })
    .vuetify()
;


if (mix.inProduction()) {
    mix.version()
}
