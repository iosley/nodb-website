<?php

return call_user_func(function() {

  $getRoutes = function($app, $directory) use (&$getRoutes) {
    if ( ! ( is_dir($directory) && $dir = opendir( $directory ) ) ) return;

    while ( false !== ($file = readdir($dir)) ) {
      $filePath = "{$directory}/{$file}";

      if ( is_file($filePath) && substr($file, -10) == '.route.php' ) {
        include_once $filePath;
      }

      if ( is_dir($filePath) && $file != '.' && $file != '..' && $file != '/' ) {
        $getRoutes($app, $filePath);
      }
    }
  };

  return $getRoutes;
});