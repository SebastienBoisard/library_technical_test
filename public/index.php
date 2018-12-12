<?php

require __DIR__ . '/../vendor/autoload.php';

use Library\Routing\Router;
use Library\Util\Logger;
use Library\Rest\BooksActions;
use Library\Rest\AuthorsActions;


$router = new Router();

$router->addRoute(Router::GET,  "/books",                BooksActions::class,   'handleGetBooksAction');
$router->addRoute(Router::GET,  "/books/",               BooksActions::class,   'handleGetBooksAction');
$router->addRoute(Router::GET,  "/books/:id",            BooksActions::class,   'handleGetBookWitdIdAction');
$router->addRoute(Router::POST, "/books",                BooksActions::class,   'handlePostBookAction');
$router->addRoute(Router::GET,  "/authors/:name/books",  AuthorsActions::class, 'handleGetBooksFromAuthorAction');


$router->handleRoutes();

