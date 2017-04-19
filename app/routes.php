<?php

// Home page
$app->get('/', "Blog\Controller\HomeController::indexAction")
->bind('home');

// Detailed info about an billet
$app->match('/billet/{id}', "Blog\Controller\HomeController::billetAction")
->bind('billet');

// Login form
$app->get('/login', "Blog\Controller\HomeController::loginAction")
->bind('login');

// Admin zone
$app->get('/admin', "Blog\Controller\AdminController::indexAction")
->bind('admin');

// Add a new billet
$app->match('/admin/billet/add', "Blog\Controller\AdminController::addBilletAction")
->bind('admin_billet_add');

// Edit an existing billet
$app->match('/admin/billet/{id}/edit', "Blog\Controller\AdminController::editBilletAction")
->bind('admin_billet_edit');

// Remove an billet
$app->get('/admin/billet/{id}/delete', "Blog\Controller\AdminController::deleteBilletAction")
->bind('admin_billet_delete');

// Edit an existing comment
$app->match('/admin/comment/{id}/edit', "Blog\Controller\AdminController::editCommentAction")
->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', "Blog\Controller\AdminController::deleteCommentAction")
->bind('admin_comment_delete');

// Remove a comment
$app->get('/admin/comment/{id}/publish', "Blog\Controller\AdminController::publishCommentAction")
->bind('admin_comment_publish');

// Remove a comment
$app->get('/admin/comment/{id}/dontpublish', "Blog\Controller\AdminController::dontpublishCommentAction")
->bind('admin_comment_dontpublish');


// Add a user
$app->match('/admin/user/add', "Blog\Controller\AdminController::addUserAction")
->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', "Blog\Controller\AdminController::editUserAction")
->bind('admin_user_edit');

// Remove a user
$app->get('/admin/user/{id}/delete', "Blog\Controller\AdminController::deleteUserAction")
->bind('admin_user_delete');

