let path = require('path');
const mix = require('laravel-mix');
const { VuetifyLoaderPlugin } = require('vuetify-loader')

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
    .js('resources/js/app.js', 'public/build/app.js')
    .vue({
      version: 2,
      extractStyles: true,
      useVueStyleLoader: true,
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
                },
            ]
        },
        plugins:[
          new VuetifyLoaderPlugin(),
        ]
    })
    .sass('resources/sass/app.scss', 'public/build/global.css')
    .browserSync({
        proxy: 'ica:8000',
        port: 3000,
        open: false,
    })
;

if (mix.inProduction()) {
    mix.version()
} else {
    mix.webpackConfig({
        devtool: 'inline-source-map'
    })
}
