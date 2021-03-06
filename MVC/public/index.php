<?php
/** Front Controller */

/* namespace + autoload 활용 -> 코드 삭제
// Controller class
require_once '../App/Controllers/PostsController.php';
// Routing
require_once '../Core/Router.php';
*/
session_start();
require_once dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function ($class){
    $root = dirname(__DIR__); // 부모 directory 저장
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)) {
        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
});

$router = new Core\Router(); // namespace 적용

// Add the routes
$router->add('', ['controller' => 'Login', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
//$router->add('{controller}/{post:\d+}/{action}');
$router->add('{controller}/{action}/{page:\d+}');
$router->add('{controller}/{action}/{param:[A-Za-z0-9+]*}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);


$url = $_SERVER['QUERY_STRING'];

//$router->add('login', ['controller' => 'Login', 'action' => 'index']);

/* dispatch 함수를 통해 일치 여부 검사 (삭제)
// Display the routing table
echo '<pre>';
//var_dump($router->getRoutes());
echo htmlspecialchars(print_r($router->getRoutes(), true));
echo '</pre>';

// Match the requested route
$url = $_SERVER['QUERY_STRING'];

if ($router->match($url)) {
    echo '<pre>';
    var_dump($router->getParams());
    echo '</pre>';
} else {
    echo "No route found for URL '$url'";
}
*/

try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch (Exception $e) {
}

/*************라우팅 vs dispatch************/
// 1. routing : asking for directions
// 2. dispatching : following those directions

// controller object 생성 -> action method 실행
// 클래스 - StudlyCaps (PSR1)
// 메소드 - camelCase
