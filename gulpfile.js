var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    var vendor_path = './vendor/';

    var paths = {
      'jquery' : vendor_path + 'components/jquery/',
      'bootstrap' : vendor_path + 'twbs/bootstrap-sass/assets/',
      'fontawesome' : vendor_path + 'components/font-awesome/'
    };


    // get sassy
    // mix.sass('app.scss','resources/css');
    // mix.sass('../../../vendor/twbs/bootstrap-sass/assets/stylesheets/_bootstrap.scss','resources/css/bootstrap.css');
    // mix.sass('../../../vendor/components/font-awesome/scss/font-awesome.scss','resources/css/font-awesome.css');

    mix.sass('app.scss', 'public/css/all.css', {
      includePaths : [
        paths.bootstrap + 'stylesheets',
        paths.fontawesome + 'scss'
      ]
    });

    //styles
    // mix.styles([
    //   'app.css',
    //   'bootstrap.css',
    //   'font-awesome.css',
    // ], 'public/css/all.css');

    // scripts
    mix.scripts([
      paths.jquery + 'jquery.js',
      'app.js',
      paths.bootstrap + 'javascripts/bootstrap.js',
    ], 'public/js/all.js')
});
