const mix = require('laravel-mix');

   mix.js('resources/js/app.js', 'public/js')
   //.extract(['moment'])
   .sass('resources/sass/app.scss', 'public/css')
   .styles('node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.ttf', 'public/webfonts/fa-solid-900.ttf')
   .styles('node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2', 'public/webfonts/fa-solid-900.woff2')
   .scripts([
   'node_modules/moment/dist/moment.js',
], 'public/admin/js/moment.js') 
   .sourceMaps()
   .version();