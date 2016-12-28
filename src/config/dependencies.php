<?php
// DIC configuration

$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
  $settings = $c->get('settings')['twig'];
  $view = new \Slim\Views\Twig($settings['template_path'], [
    // 'cache' => getenv('PHP_ENV') == 'development' ? false : $settings['cache']
  ]);

  $view->addExtension(new Twig_Extensions_Extension_Text());
  $view->addExtension(new Bes\Twig\Extension\MobileDetectExtension());

  $slug = require __DIR__ . '/../libs/slug.php';

  $view->getEnvironment()->addFilter(new Twig_SimpleFilter('slug', $slug));


  // Instantiate and add Slim specific extension
  $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

  return $view;
};

// Monolog
$container['logger'] = function ($c) {
  $settings = $c->get('settings')['logger'];
  $logger = new Monolog\Logger($settings['name']);
  $logger->pushProcessor(new Monolog\Processor\UidProcessor());
  $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
  return $logger;
};

/** Read database files */
$container['crud'] = function ($c) {
  $settings = $c->get('settings')['database'];

  /** CRUD functions */
  $read = require __DIR__ . '/../libs/readData.php';

  $getPath = function($name) use ($settings) {
    $getPath  = require __DIR__ . '/../libs/getPath.php';
    return $getPath($settings['path'], $name, $settings['ext'], $settings['prefix'], $settings['sufix']);
  };

  $crud['read'] = function($name) use ($getPath, $settings, $read) {
    $path = $getPath($name);
    $data = $read($path);

    return $data;
  };

  return $crud;
};

$container['notFoundHandler'] = function ($c) {
  return function ($request, $response) use ($c) {
    return $c->view->render($response->withStatus(404), "layouts/404.twig");
  };
};

$container['errorHandler'] = function ($c) {
  return function ($request, $response) use ($c) {
    return $c->view->render($response->withStatus(500), "layouts/500.twig");
  };
};

$container['phpErrorHandler'] = function ($c) {
  return function ($request, $response) use ($c) {
    return $c->view->render($response->withStatus(500), "layouts/500.twig");
  };
};