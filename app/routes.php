<?php

// Home page
$app->get('/', function () use ($app) {
    $billets = $app['dao.billet']->findAll();
    return $app['twig']->render('index.html.twig', array('billets' => $billets));
})->bind('home');

// Billet details with comments
$app->get('/billet/{id}', function ($id) use ($app) {
    $billet = $app['dao.billet']->find($id);
    $comments = $app['dao.comment']->findAllByBillet($id);
    return $app['twig']->render('billet.html.twig', array('billet' => $billet, 'comments' => $comments));
})->bind('billet');