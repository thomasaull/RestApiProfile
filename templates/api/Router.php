<?php namespace ProcessWire;

// Stuff taken from https://gist.github.com/clsource/dc7be74afcbfc5fe752c
// and Example Code from @lostkobrakai
// and some stuff I put in there by myself

require_once "{$config->paths->root}/vendor/autoload.php";
require_once dirname(__FILE__) . "/ApiHelper.php";
require_once dirname(__FILE__) . "/Auth.php";
require_once dirname(__FILE__) . "/Test.php";

use \Firebase\JWT\JWT;

$content = Router::go(function(\FastRoute\RouteCollector $r)
{
  $r->addRoute('GET', '/', ApiHelper::class . '@noEndpoint');
  $r->addRoute('POST', '/', ApiHelper::class . '@noEndpoint');
  $r->addRoute('PUT', '/', ApiHelper::class . '@noEndpoint');
  $r->addRoute('PATCH', '/', ApiHelper::class . '@noEndpoint');
  $r->addRoute('DELETE', '/', ApiHelper::class . '@noEndpoint');

  $r->addGroup('/auth', function (\FastRoute\RouteCollector $r)
  {
    $r->addRoute('GET', '', Auth::class . '@auth');
    $r->addRoute('POST', '', Auth::class . '@login');
    $r->addRoute('DELETE', '', Auth::class . '@logout');
  });

  $r->addGroup('/test', function (\FastRoute\RouteCollector $r)
  {
    $r->addRoute('GET', '', Test::class . '@getSomeData');
    $r->addRoute('POST', '', Test::class . '@postWithSomeData');
  });
});

class Router
{
  /**
   * @param callable $callback Route configurator
   * @param string   $path Optionally overwrite the default of using the whole urlSegmentStr
   * @throws Wire404Exception
   */
  public static function go(callable $callback, $path = '')
  {
    $dispatcher = \FastRoute\simpleDispatcher($callback);

    $routeInfo = $dispatcher->dispatch(
      $_SERVER['REQUEST_METHOD'],
      strlen($path)
        ? '/' . trim($path, '/')
        : '/' . wire('input')->urlSegmentStr
    );

    switch ($routeInfo[0]) {
      case \FastRoute\Dispatcher::NOT_FOUND:
      case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        throw new Wire404Exception();
        return;

      case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode('@', $handler, 2);
        return Router::handle($class, $method, $vars);
    }
  }


  public static function handle($class, $method, $vars)
  {
    $authActive = true;
    // if(!$authActive) \TD::fireLog('WARNING: Auth disabled!');

    header("Content-Type: application/json");
    $return = new \StdClass();
    $vars = (object) $vars;

    // if regular and not auth request, check Authorization:
    // otherwise go right through regular api handling
    if($authActive === true && $class !== Auth::class)
    {
      // convert all headers to lowercase:
      $headers = array();
      foreach(apache_request_headers() as $key => $value) {
        $headers[strtolower($key)] = $value;
      }

      // check for auth header
      if(!array_key_exists('authorization', $headers)) {
        http_response_code(400);
        $return->error = 'No Authorization Header found';
        echo json_encode($return);
        return;
      };

      // Check if jwtSecret is in config
      if(!isset(wire('config')->jwtSecret)) {
        http_response_code(500);
        $return->error = 'incorrect site config';
        echo json_encode($return);
        return;
      }

      try {
        $secret = wire('config')->jwtSecret;
        list($jwt) = sscanf($headers['authorization'], 'Bearer %s');
        $decoded = JWT::decode($jwt, $secret, array('HS256'));
      }
      catch (\Exception $e) 
      {
        http_response_code(401);
        return;
      }
    }

    // If the code runs until here, the request is authenticated
    // or the request does not need authentication
    // Get Data:
    try {
      // merge url $vars with params
      $vars = (object) array_merge((array) Router::params(), (array) $vars);
      $data = $class::$method($vars);

      if(gettype($data) == "string") $return->message = $data;
      else $return = $data;
    } 
    catch (\Exception $e) {
      $responseCode = 404;
      $return->error = $e->getMessage();
      \ProcessWire\wire('log')->error($e->getMessage());

      if($e->getCode()) $responseCode = $e->getCode();
      http_response_code($responseCode);
    }
  
    echo json_encode($return);
  }


  public static function params($index=null, $default = null, $source = null) 
  {
    // check for php://input and merge with $_REQUEST
      if ((isset($_SERVER["CONTENT_TYPE"]) &&
        stripos($_SERVER["CONTENT_TYPE"],'application/json') !== false) ||
        (isset($_SERVER["HTTP_CONTENT_TYPE"]) &&
        stripos($_SERVER["HTTP_CONTENT_TYPE"],'application/json') !== false) // PHP build in Webserver !?
        ) {
        if ($json = json_decode(@file_get_contents('php://input'), true)) {
          $_REQUEST = array_merge($_REQUEST, $json);
        }
      }

      $src = $source ? $source : $_REQUEST;

      //Basic HTTP Authetication
      if (isset($_SERVER['PHP_AUTH_USER'])) {
      $credentials = [
      "uname" => $_SERVER['PHP_AUTH_USER'],
      "upass" => $_SERVER['PHP_AUTH_PW']
      ];
      $src = array_merge($src, $credentials);
      }

      return Router::fetch_from_array($src, $index, $default);
  }


  public static function fetch_from_array(&$array, $index=null, $default = null) 
  {
    if (is_null($index)) 
    {
      return $array;
    } 
    elseif (isset($array[$index])) 
    {
      return $array[$index];
    } 
    elseif (strpos($index, '/')) 
    {
      $keys = explode('/', $index);

      switch(count($keys))
      {
        case 1:
          if (isset($array[$keys[0]])){
            return $array[$keys[0]];
          }
          break;

        case 2:
          if (isset($array[$keys[0]][$keys[1]])){
            return $array[$keys[0]][$keys[1]];
          }
          break;

        case 3:
          if (isset($array[$keys[0]][$keys[1]][$keys[2]])){
            return $array[$keys[0]][$keys[1]][$keys[2]];
          }
          break;

        case 4:
          if (isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
            return $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]];
          }
          break;
      }
    }

    return $default;
  }


  public static $statusCodes = array(
    // Informational 1xx
    100 => 'Continue',
    101 => 'Switching Protocols',
    // Successful 2xx
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    // Redirection 3xx
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    // Client Error 4xx
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Request Range Not Satisfiable',
    417 => 'Expectation Failed',
    // Server Error 5xx
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported'
  );
}