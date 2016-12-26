<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

call_user_func(function() use ($app) {
  $getRoutes = require __DIR__ . '/../libs/getRoutes.php';

  /** Rota index */
  if (!is_file(__DIR__ . '/../pages/index.route.php') && !is_file(__DIR__ . '/../pages/index/index.route.php')) {
    $app->get('[/]', function(Request $request, Response $response, $args = []) {
      $data["site"] = $this->crud['read']('site');
      $data["page"] = $this->crud['read']('index');

      $template = is_file(__dir__ . "/../pages/index.twig")
        ? "index.twig"
        : is_file(__dir__ . "/../pages/index/index.twig")
        ? "index/index.twig"
        : NULL;

      if ( empty($template) ) {
        throw new \Slim\Exception\NotFoundException($request, $response);
      }

      return $this->view->render($response, "index.twig", (array) $data);
    });
  }

  /** Carrega rotas definidas na pasta pages */
  $getRoutes($app, __DIR__ . '/../pages/');

  /** Rotas automaticas (default) */
  $app->get('/{page}[/[{params:.*}[/]]]', function(Request $request, Response $response, Array $args = []) {
    $slug = require __dir__ . '/../libs/slug.php';

    $data['site'] = $this->crud['read']('site');
    $data['page'] = $this->crud['read']($args['page']);
    $data['raiz'] = $slug($args['page']);

    $template = $args['page'];
    if ( isset($args['params']) && is_numeric($args['params']) || (! empty($args['params']) && ! empty($data['page'])) ) {

      $params = explode('/', $args['params']);

      foreach ($params as $param) {
        if (!is_numeric($param) && empty($param)) continue;
        $template .= '.' . $slug($param);
        if ( is_object($data['page']) ) {
          $data['page'] = empty($data['page']->$param) ? new StdClass() : $data['page']->$param;
        } else if (is_array($data['page'])) {
          if (empty($data['page'][$param])) {
            $tmp = new StdClass();
            foreach ($data['page'] as $key => $value) {
              if (
                  (!empty($value->title) && $slug($value->title) == $param)
                  || (!empty($value->titulo) && $slug($value->titulo) == $param)
                  || (!empty($value->id) && $slug($value->id) == $param)
                ) {
                $tmp = $value;
                break;
              }
            }
            $data['page'] = $tmp;
          } else {
            $data['page'] = $data['page'][$param];
          }
        } else {
          $data['page'] = new StdClass();
        }
      }
    }

    $template = is_file(__dir__ . "/../pages/{$template}.twig")
      ? __dir__ . "/../pages/{$template}.twig"
      : is_file(__dir__ . "/../pages/{$args['page']}/{$template}.twig")
      ? __dir__ . "/../pages/{$args['page']}/{$template}.twig"
      : NULL;

    if ( empty($template) ) {
      throw new \Slim\Exception\NotFoundException($request, $response);
    }

    /** Search */
    if (!empty($_GET)) {
      if (is_object($data['page'])) {
        $cond = FALSE;
        foreach ($_GET as $key => $value) {
          if (empty($data['page']->$key)) {
            $cond = FALSE;
            break;
          }

          if (is_array($data['page']->$key)) {
            $arr = array_values(array_filter($data['page']->$key, function($item) use ($slug, $value) {
              if ($slug(trim($item)) == $slug(trim($value))) {
                return TRUE;
              }
              return FALSE;
            }));
            if (empty($arr)) {
              $cond = FALSE;
              break;
            }
            $cond = TRUE;
            continue;
          }

          if ($data['page']->$key == $value) {
            $cond = TRUE;
            continue;
          }

          $cond = FALSE;
          break;
        }
        if (!$cond) {
          $data['page'] = new StdClass();
        }
      } else if (is_array($data['page'])) {
        $data['page'] = array_values(array_filter($data['page'], function($item) use ($slug) {
          $cond = FALSE;
          foreach ($_GET as $key => $value) {
            if (is_object($item)){
              if (empty($item->$key) || is_object($item->$key)) {
                $cond = FALSE;
                break;
              }
              if (is_array($item->$key)) {
                foreach ($item->$key as $i => $val) {
                  if (is_array($val) || is_object($val)) {
                    $cond = FALSE;
                    break 2;
                  }
                  if ( $slug(trim($val)) == $slug(trim($value)) ) {
                    $cond = TRUE;
                    continue;
                  }
                }
                continue;
              }

              if ( $slug(trim($item->$key)) == $slug(trim($value)) ) {
                $cond = TRUE;
                continue;
              }
            } else if (is_array($item)) {
            } else {}
          }
          return $cond;
        }));
      }
    }

    return $this->view->render($response, $template, (array) $data);
  });
});