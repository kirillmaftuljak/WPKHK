let mix = require('laravel-mix')

mix.setResourceRoot('../../')

mix.setPublicPath('public')

const webpack = require('webpack')

mix
  .js('assets/js/backend/amelia-booking.js', 'public/js/backend')
  .js('assets/js/frontend/amelia-booking.js', 'public/js/frontend')
  .less('assets/less/backend/amelia-booking.less', 'public/css/backend')
  .less('assets/less/external/vendor.less', 'public/css/frontend')
  .less('assets/less/frontend/amelia-booking.less', '../../../uploads/amelia/css')
  .copyDirectory('assets/img', 'public/img')
  .copyDirectory('assets/js/tinymce', 'public/js/tinymce')
  .copyDirectory('assets/js/gutenberg', 'public/js/gutenberg')
  .webpackConfig({
    entry: {
      app: ['idempotent-babel-polyfill', './assets/js/backend/amelia-booking.js', './assets/js/frontend/amelia-booking.js']
    },
    output: {
      chunkFilename: process.env.NODE_ENV !== 'production' ? 'js/chunks/amelia-booking-[name].js' : 'js/chunks/amelia-booking-[name]-[hash].js',
      publicPath: '',
      jsonpFunction: 'wpJsonpAmeliaBookingPlugin'
    },
    plugins: [
      new webpack.DefinePlugin({
        'AMELIA_LITE_VERSION': true
      })
    ]
  })

if (!mix.inProduction()) {
  mix.sourceMaps()
}
