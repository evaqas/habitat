const path    = require('path')
const project = require('./package.json')
const Encore  = require('@symfony/webpack-encore')

const assetsHash = Encore.isProduction() ? '.[contenthash:8]' : ''
const imagesHash = Encore.isProduction() ? '.[hash:8]' : ''

const config = {
    entries: [
        './assets/js/main.js',
        './assets/js/page.js',
    ],
    assetsDir: 'assets/dist',
    publicPath: '',//`/app/themes/${project.name}`,
    distPath: 'dist',
    names: {
        js: `js/[name]${assetsHash}.js`,
        css: `css/[name]${assetsHash}.css`,
        images: `[path][name]${imagesHash}.[ext]`,
        // vendors: 'vendors',
    },
    copyFolders: [ 'images', 'svg' ],
    enableVue: false,
    showStats: true,
}

if ( ! Encore.isRuntimeEnvironmentConfigured() ) {
    Encore.configureRuntimeEnvironment( process.env.NODE_ENV || 'dev' );
}

for ( const entryFile of config.entries ) {
    Encore.addEntry( path.parse( entryFile ).name, entryFile )
}

Encore
    .setOutputPath( config.assetsDir )
    .setPublicPath( `${config.publicPath}/${config.assetsDir}` )
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
    .enableSingleRuntimeChunk()
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

const webPackConfig = Encore.getWebpackConfig()

if ( config.showStats ) {
    webPackConfig.stats.assets = true
    webPackConfig.stats.assetsSort = 'index'
}

module.exports = webPackConfig
