// WordPress webpack config.
const defaultConfig             = require( '@wordpress/scripts/config/webpack.config' );

// Plugins.
const RemoveEmptyScriptsPlugin  = require( 'webpack-remove-empty-scripts' );
const CopyPlugin                = require( 'copy-webpack-plugin' );

// Utilities.
const path                      = require( 'path' );

// Add any a new entry point by extending the webpack config.
module.exports = {

  ...defaultConfig,
  ...{

    entry: {

      // Spread the auto-generated entrypoints.
      ...defaultConfig.entry(),

      'js/editor':  path.resolve( process.cwd(), 'src/js',  'editor.js'   ),
      'css/editor': path.resolve( process.cwd(), 'src/css', 'editor.scss' ),
      'js/main':    path.resolve( process.cwd(), 'src/js',  'main.js'   ),
      'css/screen': path.resolve( process.cwd(), 'src/css', 'screen.scss' ),

    },

    plugins: [

      // Include WP's plugin config.
      ...defaultConfig.plugins,

      // Removes the empty `.js` files generated by webpack but sets it after WP has generated its `*.asset.php` file.
      new RemoveEmptyScriptsPlugin( {
        stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS
      } ),

      // Copies any assets that don't need to be processed to the output folder.
      new CopyPlugin( {
        patterns: [
          {
            from: './src/fonts',
            to:   './fonts'
          },
          {
            from: './src/images',
            to:   './images'
          }
        ]
      } )
    ]
  }
};