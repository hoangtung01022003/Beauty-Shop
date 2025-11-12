<?php
/**
 * =====================================================
 * ROUTER CLASS - Quản lý routing và dispatch requests
 * =====================================================
 * File: config/Router.php
 * Mô tả: Xử lý URL routing và gọi controller tương ứng
 * =====================================================
 */

class Router
{
    /**
     * Danh sách routes
     */
    private static $routes = [];

    /**
     * Controller mặc định
     */
    private static $defaultController = 'ProductController';

    /**
     * Action mặc định
     */
    private static $defaultAction = 'home';

    /**
     * Thêm route GET
     */
    public static function get($path, $handler)
    {
        self::addRoute('GET', $path, $handler);
    }

    /**
     * Thêm route POST
     */
    public static function post($path, $handler)
    {
        self::addRoute('POST', $path, $handler);
    }

    /**
     * Thêm route (bất kỳ method nào)
     */
    private static function addRoute($method, $path, $handler)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Dispatch request đến controller tương ứng
     */
    public static function dispatch()
    {
        try {
            // Lấy request method và URI
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUri = $_SERVER['REQUEST_URI'];

            // Parse URI (bỏ query string và base path)
            $uri = self::parseUri($requestUri);

            // Tìm route phù hợp
            $matched = false;

            foreach (self::$routes as $route) {
                // Kiểm tra method
                if ($route['method'] !== $requestMethod) {
                    continue;
                }

                // Kiểm tra path
                $pattern = self::convertPathToRegex($route['path']);

                if (preg_match($pattern, $uri, $matches)) {
                    $matched = true;

                    // Lấy params từ URL
                    array_shift($matches); // Bỏ phần tử đầu tiên (full match)

                    // Gọi handler
                    self::callHandler($route['handler'], $matches);
                    break;
                }
            }

            // Nếu không tìm thấy route, xử lý fallback
            if (!$matched) {
                self::handleFallback($uri, $requestMethod);
            }

        } catch (Exception $e) {
            self::handleError($e);
        }
    }

    /**
     * Parse URI từ request
     */
    private static function parseUri($requestUri)
    {
        // Lấy base path từ constants
        $basePath = BASE_PATH;

        // Parse URL
        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'] ?? '/';

        // Bỏ base path
        if (!empty($basePath) && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // Trim slashes
        $path = trim($path, '/');

        return $path;
    }

    /**
     * Convert path thành regex pattern
     */
    private static function convertPathToRegex($path)
    {
        // Escape slashes
        $pattern = str_replace('/', '\/', $path);

        // Convert {param} thành regex group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^\/]+)', $pattern);

        // Thêm anchors
        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }

    /**
     * Gọi handler (controller@action hoặc closure)
     */
    private static function callHandler($handler, $params = [])
    {
        if (is_callable($handler)) {
            // Handler là closure
            call_user_func_array($handler, $params);
        } elseif (is_string($handler)) {
            // Handler là string: "Controller@action"
            $parts = explode('@', $handler);
            $controllerName = $parts[0];
            $actionName = $parts[1] ?? 'index';

            self::callController($controllerName, $actionName, $params);
        } elseif (is_array($handler)) {
            // Handler là array: [Controller, action]
            $controllerName = $handler[0];
            $actionName = $handler[1] ?? 'index';

            self::callController($controllerName, $actionName, $params);
        }
    }

    /**
     * Gọi controller và action
     */
    private static function callController($controllerName, $actionName, $params = [])
    {
        // Load controller file
        $controllerFile = "controllers/{$controllerName}.php";

        // Kiểm tra file trong thư mục Admin
        if (!file_exists($controllerFile)) {
            $controllerFile = "controllers/Admin/{$controllerName}.php";
        }

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }

        require_once $controllerFile;

        // Kiểm tra class tồn tại
        if (!class_exists($controllerName)) {
            throw new Exception("Controller class not found: {$controllerName}");
        }

        // Tạo instance
        $controller = new $controllerName();

        // Chuyển đổi action name từ kebab-case sang camelCase
        $actionNameCamelCase = self::toCamelCase($actionName);

        // Kiểm tra method tồn tại (thử cả kebab-case và camelCase)
        if (method_exists($controller, $actionNameCamelCase)) {
            $actionName = $actionNameCamelCase;
        } elseif (!method_exists($controller, $actionName)) {
            throw new Exception("Action not found: {$controllerName}::{$actionName}");
        }

        // Gọi action với params
        call_user_func_array([$controller, $actionName], $params);
    }

    /**
     * Chuyển đổi string từ kebab-case sang camelCase
     */
    private static function toCamelCase($string)
    {
        // Chuyển my-orders thành myOrders
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    /**
     * Xử lý fallback khi không tìm thấy route
     */
    private static function handleFallback($uri, $method)
    {
        // Nếu là request POST, redirect về trang trước
        if ($method === 'POST') {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? base_url()));
            exit;
        }

        // Parse URI thành controller/action/params
        $segments = explode('/', $uri);

        // Nếu URI rỗng, redirect về trang login
        if (empty($uri)) {
            header('Location: ' . base_url('auth/login'));
            exit;
        }

        // Lấy controller
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : self::$defaultController;

        // Lấy action
        $actionName = $segments[1] ?? self::$defaultAction;

        // Lấy params
        $params = array_slice($segments, 2);

        // Kiểm tra controller tồn tại
        $controllerFile = "controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            $controllerFile = "controllers/Admin/{$controllerName}.php";
        }

        if (file_exists($controllerFile)) {
            self::callController($controllerName, $actionName, $params);
        } else {
            self::show404();
        }
    }

    /**
     * Hiển thị trang 404
     */
    private static function show404()
    {
        http_response_code(404);

        // Kiểm tra có view 404 không
        if (file_exists('views/errors/404.php')) {
            require 'views/errors/404.php';
        } else {
            echo '<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { font-size: 72px; margin: 0; color: #667eea; }
        h2 { color: #333; }
        p { color: #666; }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Trang không tồn tại</h2>
        <p>Xin lỗi, trang bạn tìm kiếm không tồn tại hoặc đã bị xóa.</p>
        <a href="' . base_url() . '">Về trang chủ</a>
    </div>
</body>
</html>';
        }
        exit;
    }

    /**
     * Xử lý lỗi
     */
    private static function handleError($exception)
    {
        // Log error
        error_log($exception->getMessage());

        // Nếu là môi trường development, hiển thị error
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo '<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .error { background: white; padding: 20px; border-left: 4px solid #f44336; }
        h1 { color: #f44336; }
        pre { background: #333; color: #fff; padding: 15px; overflow: auto; }
    </style>
</head>
<body>
    <div class="error">
        <h1>Application Error</h1>
        <p><strong>Message:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>
        <p><strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . '</p>
        <p><strong>Line:</strong> ' . $exception->getLine() . '</p>
        <h2>Stack Trace:</h2>
        <pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>
    </div>
</body>
</html>';
        } else {
            // Môi trường production, hiển thị trang lỗi chung
            http_response_code(500);
            echo '<!DOCTYPE html>
<html>
<head>
    <title>500 - Server Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { font-size: 72px; margin: 0; color: #f44336; }
        h2 { color: #333; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500</h1>
        <h2>Lỗi Server</h2>
        <p>Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại sau.</p>
    </div>
</body>
</html>';
        }
        exit;
    }
}
