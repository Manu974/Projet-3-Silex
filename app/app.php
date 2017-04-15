<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));


// Register services.
$app['dao.billet'] = function ($app) {
    return new Blog\DAO\BilletDAO($app['db']);
};

$app['dao.comment'] = function ($app) {
    $commentDAO = new Blog\DAO\CommentDAO($app['db']);
    $commentDAO->setBilletDAO($app['dao.billet']);
    return $commentDAO;
};