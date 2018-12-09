<?php

require __DIR__ . '/../vendor/autoload.php';

use Library\Routing\Router;
use Library\Util\Logger;
use Library\Rest\BooksRest;
use Library\Rest\AuthorsRest;


$router = new Router();

$router->addRoute(Router::GET,  "/books",                BooksRest::class);
$router->addRoute(Router::GET,  "/books/",               BooksRest::class);
$router->addRoute(Router::GET,  "/books/:id",            BooksRest::class);
$router->addRoute(Router::POST, "/books",                BooksRest::class);
$router->addRoute(Router::GET,  "/authors/:name/books",  AuthorsRest::class);


$router->handleRoutes();

