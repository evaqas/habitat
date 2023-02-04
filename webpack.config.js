const path              = require('path')
const project           = require('./package.json')
const Encore            = require('@symfony/webpack-encore')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')

if ( ! Encore.isRuntimeEnvironmentConfigured() ) {
    Encore.configureRuntimeEnvironment( process.env.NODE_ENV || 'dev' );
}

const contentHash = Encore.isProduction() ? '.[contenthash:8]' : ''
const buildHash   = Encore.isProduction() ? '.[hash:8]' : ''

const config = {
    entries: [ './assets/js/main.js' ],
    assetsPath: 'assets',
    distPath: 'dist',
    publicPath: `/wp-content/themes/${project.name}`,
    names: {
        js: `js/[name]${contentHash}.js`,
        css: `css/[name]${contentHash}.css`,
    },
    copyFolders: [ 'fonts', 'images', 'svg' ],
    enableVue: false,
    showStats: true,
    host: project.name + '.local',
    certsPath: '/Users/evaldas/Library/Application Support/Local/run/router/nginx/certs/',
}

for ( const entryFile of config.entries ) {
    Encore.addEntry( path.parse( entryFile ).name, entryFile )
}

Encore
    .setOutputPath( config.distPath )
    .setPublicPath( `${config.publicPath}/${config.distPath}` )
    .enableVersioning( Encore.isProduction() )
    .configureFilenames( config.names )
    .setManifestKeyPrefix('')
    .configureManifestPlugin( options => {
        options.generate = (seed, files, entrypoints) => files.reduce( (manifest, {name: fileName, path: filePath}) =>
        {
            const srcPath = path.basename( path.dirname( fileName ) )
            const targetPath = path.basename( path.dirname( filePath ) )

            if ( srcPath !== targetPath ) {
                fileName = `${targetPath}/${fileName}`
            }

            if ( ! (`${fileName}` in manifest) ) {
                manifest[ fileName ] = filePath
            }

            return manifest
        }, seed )
    })
    .splitEntryChunks()
    .configureSplitChunks( splitChunks => {
        splitChunks.minSize = 1024 * 30
        splitChunks.maxInitialRequests = 3
        splitChunks.cacheGroups = {
            vendors: {
                test: /[\\/]node_modules[\\/]/,
                // name: config.names.vendors,
                name (module, chunks, cacheGroupKey) {
                    const allChunksNames = chunks.map((item) => item.name).join('~');
                    return `${cacheGroupKey}-${allChunksNames}`;
                },
                chunks: 'all',
            }
        }
    } )
    .enableSassLoader()
    .enablePostCssLoader()
    .copyFiles(
        config.copyFolders.map( folderName => ({
            from: `${config.assetsPath}/${folderName}`,
            to: `${folderName}/[path][name]${buildHash}.[ext]`,
        }) )
    )
    .configureDevServerOptions( options => {
        options.contentBase = path.resolve( __dirname, './' ), // absolute path
        options.publicPath = `${config.publicPath}/${config.distPath}` // webpack assets path
        options.writeToDisk = true
    } )
    .addPlugin( new BrowserSyncPlugin({
        // host: host,
        proxy: 'https://' + config.host,
        https: {
            key : config.certsPath + config.host + '.key',
            cert: config.certsPath + config.host + '.crt',
        },
        open: false,
        files: [ '**/*.{php,twig,jpg,png,svg}' ],
        ignore: [ 'node_modules', 'vendor' ],
    }) )

if ( config.entries.length >= 2 ) {
    Encore.enableSingleRuntimeChunk()
} else [
    Encore.disableSingleRuntimeChunk()
]

if ( config.enableVue ) {
    Encore.enableVueLoader()
}

const webPackConfig = Encore.getWebpackConfig()

if ( config.showStats ) {
    webPackConfig.stats.assets = true
    webPackConfig.stats.assetsSort = 'index'
}

module.exports = webPackConfig
