<?php

class Router
{
    protected $routes = [
        'get' => [],
        'post' => []
    ];
    protected $request;
    protected $adminPath = 'admin'; // Default value

    public function __construct(Request $request)
    {
        $this->request = $request;

        // Fetch custom admin path from DB
        try {
            $db = getDbConnection();
            $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'admin_path' LIMIT 1");
            $path = $stmt->fetchColumn();
            if ($path) {
                $this->adminPath = $path;
            }
        } catch (Exception $e) {
            // If DB or table doesn't exist yet, just use the default.
        }
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function adminGroup(callable $callback)
    {
        // Save original routes
        $originalRoutes = $this->routes;

        // Temporarily clear routes to capture only the ones defined in the callback
        $this->routes = ['get' => [], 'post' => []];

        // Call the user function, which will define the admin routes
        $callback($this);

        // Prepend the admin path to the newly defined routes and merge back
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $path => $cb) {
                $prefixedPath = '/' . $this->adminPath . ($path === '/' ? '' : $path);
                $originalRoutes[$method][$prefixedPath] = $cb;
            }
        }

        // Restore the routes
        $this->routes = $originalRoutes;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $routes = $this->routes[$method];

        foreach ($routes as $route => $callback) {
            // Convert route to regex: /users/{id} -> /users/(\w+)
            $pattern = "~^" . preg_replace('/\{(\w+)\}/', '(\w+)', $route) . "$~";

            if (preg_match($pattern, $path, $matches)) {
                // Remove the full match from the beginning of the array
                array_shift($matches);

                if (is_string($callback)) {
                    $parts = explode('@', $callback);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        if (method_exists($controller, $methodName)) {
                            // Prepend the request object to the parameters
                            $params = array_merge([$this->request], $matches);
                            return call_user_func_array([$controller, $methodName], $params);
                        }
                    }
                }

                if (is_callable($callback)) {
                    return call_user_func_array($callback, $matches);
                }
            }
        }

        // If no route was matched
        http_response_code(404);
        // I will use the existing 404 view for consistency
        require_once '../views/errors/404.php';
        exit;
    }
}
