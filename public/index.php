<?php

namespace Public\Index;

require __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;

$router = new Router();

$router->get('/home', function ()
{
    $controller = new MatchController.php;
    $controller->allMatches();
});

$router->run();