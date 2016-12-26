<?php
/** getPath() */

return function($path, $name, $ext, $prefix, $sufix) {
  if ( substr($path, -1) != '/' ) {
    $path .= '/';
  }

  if ( ! empty($prefix) ) {
    $name = $prefix . $name;
  }

  if ( ! empty($sufix) ) {
    $name .= $sufix;
  }

  if ( substr($ext, 0, 1) != '.' ) {
    $ext = '.' . $ext;
  }

  return $path . $name . $ext;
};