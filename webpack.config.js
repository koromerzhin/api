const Encore            = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore.setOutputPath('public/build/');
Encore.setPublicPath('/build');
// Encore.addEntry('app', './assets/ts/app.ts');
Encore.addEntry('app', [
    './assets/js/app.js'
]);
Encore.splitEntryChunks();
Encore.enableSingleRuntimeChunk();
Encore.cleanupOutputBeforeBuild();
Encore.enableBuildNotifications();
Encore.enableSourceMaps(!Encore.isProduction());
Encore.enableVersioning(Encore.isProduction());
Encore.configureBabel(() => {}, {
    'useBuiltIns': 'usage',
    'corejs'     : 3
} );
Encore.configureUrlLoader( {
    'images': {
        'limit': 4096
    }
} )
Encore.enableSassLoader();
Encore.enableLessLoader();
Encore.autoProvidejQuery();
Encore.autoProvideVariables( {
    '$'            : 'jquery',
    'jQuery'       : 'jquery',
    '$.formBuilder': 'formBuilder',
    'window.jQuery': 'jquery'
} );
Encore.configureBabel();
Encore.addPlugin(new CopyWebpackPlugin([{
        'from': 'node_modules/tinymce/skins',
        'to'  : 'skins'
    },
    {
        'from': 'node_modules/tinymce-i18n/langs',
        'to'  : 'langs'
    },
    {
        'from': 'node_modules/tinymce/plugins',
        'to'  : 'plugins'
    },
    {
        'from': 'node_modules/formbuilder-languages',
        'to'  : 'formbuilder-lang'
    },
    {
        'from': 'node_modules/datatables.net-plugins/i18n',
        'to'  : 'i18n-datatables'
    }
]));
// Encore.enableTypeScriptLoader();
// Encore.enableForkedTypeScriptTypesChecking();
Encore.enableIntegrityHashes();

let webpack = Encore.getWebpackConfig();

if (Encore.isProduction()) {
    webpack.output.jsonpFunction = 'labstag';
}
module.exports = webpack;
