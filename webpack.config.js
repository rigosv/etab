var Encore = require('@symfony/webpack-encore');
const { VueLoaderPlugin } = require('vue-loader');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    // the public path you will use in Symfony's asset() function - e.g. asset('build/some_file.js')
    .setManifestKeyPrefix('build/')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // the following line enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('app/js', './assets/js/app.js')
    .addStyleEntry('app/css', './assets/css/app.scss')

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    .enableSingleRuntimeChunk()

    // uncomment for legacy applications that require $/jQuery as a global variable
    //.autoProvidejQuery()

    .enableVueLoader()
    ;


module.exports = Encore.getWebpackConfig();
