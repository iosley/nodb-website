<?php

return function($file) {
  try {
    if ( substr($file, 0, 4) != 'http' && ! file_exists($file) ) return new StdClass();

    $data = file_get_contents($file);
  } catch (Exception $e) {
    return new StdClass();
  }

  return json_decode($data);
};