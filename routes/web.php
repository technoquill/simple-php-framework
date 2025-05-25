<?php
declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Services\HomePageService;
use Technoquill\Framework\Container\Container;
use Technoquill\Framework\Router\Router;

/** @var Router $router */
/** @var Container $container */


// --------- HomeController::index

$router->get(path: '/', handler: [HomeController::class, 'index'])->name('home.index');

//$router->get('/', function () use ($container) {
//    return $container->get(HomeController::class)->index();
//})->name('home.index');


// --------- HomeController::license
//$router->get(path: '/license', handler: [HomeController::class, 'license'])
//->name('home.license')->theme('default');

$router->get('/license', function () use ($container) {
    return $container->get(HomeController::class)->license();
})->name('home.license');


// --------- HomeController::license
//$router->get(path: '/welcome/{name}', handler: [HomeController::class, 'welcome'])
//->name('home.welcome')->theme('default');

$router->get('/welcome/{name}', function (HomePageService $service) use ($container) {
    return $container->get(HomeController::class)->setParams([
        'name' => 'guest',
    ])->welcome($service);
})->name('home.welcome');

