<?php

 namespace cms\route;
 use cms\HttpKernel as HttpKernel;
/**
 * @method static Macaw get(string $route, Callable $callback)
 * @method static Macaw post(string $route, Callable $callback)
 * @method static Macaw put(string $route, Callable $callback)
 * @method static Macaw delete(string $route, Callable $callback)
 * @method static Macaw options(string $route, Callable $callback)
 * @method static Macaw head(string $route, Callable $callback)
 */
class Route {
  public static $halts = false;
  public static $routes = array();
  public static $methods = array();
  public static $callbacks = array();
  public static $patterns = array(
      ':any' => '[^/]+',
      ':num' => '[0-9]+',
      ':all' => '.*'
  );
  public static $error_callback;
  
  /**
   * Defines a route w/ callback and method
   */
  public  function __call($method, $params) {
    $uri = dirname($_SERVER['PHP_SELF']).$params[0];
    $callback = $params[1];

    array_push(self::$routes, $uri);
    array_push(self::$methods, strtoupper($method));
    array_push(self::$callbacks, $callback);
  }
  /**
   * Defines a route w/ callback and method
   */
  public static function __callstatic($method, $params) {
    $uri = dirname($_SERVER['PHP_SELF']).$params[0];
    $callback = $params[1];

    array_push(self::$routes, $uri);
    array_push(self::$methods, strtoupper($method));
    array_push(self::$callbacks, $callback);
  }

  /**
   * Defines callback if route is not found
  */
  public static function error($callback) {
    self::$error_callback = $callback;
  }

  public static function haltOnMatch($flag = true) {
    self::$halts = $flag;
  }

  /**
   * Runs the callback for the given request
   */
  public static function Dispatch($request){

    $uri = $request->uri();
    $method = $request->method();
    $searches = array_keys(static::$patterns);
    $replaces = array_values(static::$patterns);

    $found_route = false;

    self::$routes = str_replace('//', '/', self::$routes);

    // Check if route is defined without regex
    if (in_array($uri, self::$routes)) {
      
      $route_pos = array_keys(self::$routes, $uri);
      foreach ($route_pos as $route) {
        // Using an ANY option to match both GET and POST requests
        if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
          $found_route = true;

          // If route is not an object
          if (!is_object(self::$callbacks[$route])) {

            // Grab all parts based on a / separator
            $parts = explode('/',self::$callbacks[$route]);

            // Collect the last index of the array
            $last = end($parts);

            // Grab the controller name and method call
            $segments = explode('@',$last);

            // Instanitate controller
            $routeinfo['fun'] = false;
            $routeinfo['controller'] = $segments;
            return $routeinfo;
            // Call method

          } else {
            // Call closure
            $routeinfo['fun'] = self::$callbacks[$pos];
            return $routeinfo;
          }
        }
      }
    } else {
      // Check if defined with regex
      $pos = 0;
      foreach (self::$routes as $route) {
        if (strpos($route, ':') !== false) {
          $route = str_replace($searches, $replaces, $route);
        }
        if (preg_match('#^' . $route . '$#', $uri, $matched)) {
          if (self::$methods[$pos] == $method) {
            $found_route = true;

            // Remove $matched[0] as [1] is the first parameter.
            array_shift($matched);

            if (!is_object(self::$callbacks[$pos])) {

              // Grab all parts based on a / separator
              $parts = explode('/',self::$callbacks[$pos]);

              // Collect the last index of the array
              $last = end($parts);

              // Grab the controller name and method call
              $segments = explode('@',$last);

              // Instanitate controller
              $routeinfo['fun'] = false;
              $routeinfo['controller'] = $segments;
              $routeinfo['params'] = $matched;
              return $routeinfo;

            } else {
              call_user_func_array(self::$callbacks[$pos], $matched);
              $routeinfo['fun'] = self::$callbacks[$pos];
              $routeinfo['params'] = $matched;
              return $routeinfo;
            }
          }
        }
        $pos++;
      }
    }

    return false;
  }

  public function load($file)
  {
    include $file;
  }
}
