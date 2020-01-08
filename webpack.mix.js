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
    .webpackConfig({
        resolve: {
            extensions: ['.js', '.json', '.vue', '.ts'],
            alias: {
                '~': path.join(__dirname, './resources/js'),
            },
        },
        plugins: [
            new VuetifyLoaderPlugin()
        ],
        module: {
            rules: [
                {
                    test: /\.js?$/,
                    exclude: /(bower_components)/,
                    use: [{
                        loader: "babel-loader",
                        options: mix.config.babel()
                    }]
                },
                {
                    test: /\.ts?$/,
                    use: [{
                        loader: "ts-loader",
                    }]
                },
            ]
        }
    },)
    .browserSync({
        browser: "google chrome",
        proxy: '127.0.0.1:8000'
    });
;

if (mix.inProduction()) {
    mix.version();
}
