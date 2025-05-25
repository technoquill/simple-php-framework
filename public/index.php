<?php
declare(strict_types=1);

use Technoquill\Framework\Factory\AppFactory;

// Bootstrap autoload
require __DIR__.'/../bootstrap/bootstrap.php';

try {
    // Create and run an application
    $app = AppFactory::create();
    $app->run();
} catch (ReflectionException $e) {
    echo $e->getMessage();
}
