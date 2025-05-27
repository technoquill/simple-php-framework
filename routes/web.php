<?php
declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Services\HomePageService;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Middleware\AuthMiddleware;
use Technoquill\Framework\Router\Router;

/** @var Router $router */
/** @var Container $container */



//$router->group(['prefix' => '/shop', 'middleware' => []], function ($router) {
//    $router->group(['prefix' => '/category', 'middleware' => ['auth', 'access']], function ($router) {
//        $router->get(path: '/laptops', handler: [HomeController::class, 'index'])->name('shop.category.laptops');
//        $router->get(path: '/smartphones', handler: [HomeController::class, 'index'])->name('shop.category.smartphones');
//    });
//});
//
//$router->post(path: '/data', handler: [HomeController::class, 'index'])->name('data');

// --------- HomeController::index

$router->get(path: '/', handler: [HomeController::class, 'index'])->name('home.index');

//$router->get('/', function () use ($container) {
//    return $container->get(HomeController::class)->index();
//})->name('home.index');


// --------- HomeController::license
$router->get(path: '/license', handler: [HomeController::class, 'license'])->name('home.license')
    ->middleware([AuthMiddleware::class]);

//$router->get('/license', function () use ($container) {
//    return $container->get(HomeController::class)->license();
//})->name('home.license');


// --------- HomeController::license
//$router->get(path: '/welcome/{name}', handler: [HomeController::class, 'welcome'])
//    ->name('home.welcome');

$router->get('/welcome/{name}', function (HomePageService $service) use ($container) {
    return $container->get(HomeController::class)->setParams([
        'name' => 'guest',
    ])->welcome($service);
})->name('home.welcome');

