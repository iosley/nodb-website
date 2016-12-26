<?php

return call_user_func(function() {
  try {
    $appName = json_decode(file_get_contents(__DIR__ . '/../../composer.json'))->name;
  } catch (Exception $e) {
    $appName = 'WebSite';
  }

  return [
    'settings' => [
      /* set to true in development */
      'displayErrorDetails' => getenv('PHP_ENV') == 'development',
      /* Allow the web server to send the content-length header */
      'addContentLengthHeader' => true,

      /* Twig settings */
      'twig' => [
        'template_path' => [
          __DIR__ . "/../templates/",
          __DIR__ . "/../pages/"
        ],
        'cache' => __DIR__ . '/../../cache/'
      ],

      'database' => [
        'path' => __DIR__ . '/../../database/',
        'prefix' => '',
        'sufix' => '',
        'ext' => 'json'
      ],

      /* Monolog settings */
      'logger' => [
        'name' => $appName,
        'path' => __DIR__ . "/../../logs/{$appName}.log",
      ],
    ],
  ];
});
