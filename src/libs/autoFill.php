<?php

return call_user_func(function() {
  $read     = require __DIR__ . '/readData.php';
  $getPath  = require __DIR__ . '/getPath.php';
  $settings = require __DIR__ . '/../config/settings.php';
  $settings = $settings['settings']['database'];

  $reader = function($model) use ($read, $getPath, $settings, $settings) {
    $path = $getPath($settings['path'], $model, $settings['ext'], $settings['prefix'], $settings['sufix']);
    return $read($path);
  };

  $filter = require __DIR__ . '/objectFinder.php';

  $fill = function($data) use (&$fill, $reader, $filter) {
    if (is_object($data) || is_array($data)) {
      foreach ($data as $key => $value) {
        if (is_object($data)) {
          $data->$key = $fill($value, $reader);
        } else {
          $data[$key] = $fill($value, $reader);
        }
      }
      return $data;
    }

    if (is_string($data) && substr($data, 0, 1) === '@') {
      $data      = substr($data, 1);
      $dataEx    = explode('.', $data);
      $modelName = array_shift($dataEx);
      $model     = $reader($modelName);
      $model = $filter($model, $dataEx);
      return $fill($model);
    }

    return $data;
  };

  return $fill;
});