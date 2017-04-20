<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Blog\Domain\Comment;
use Blog\Form\Type\CommentType;
use Blog\Domain\User;
use Blog\Form\Type\UserType;

class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
       $billets = $app['dao.billet']->findAll();
    return $app['twig']->render('index.html.twig', array('billets' => $billets));
    }
    
    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function billetAction($id, Request $request, Application $app) {
        $billet = $app['dao.billet']->find($id);
        $commentFormView = null;
    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        // A user is fully authenticated : he can add comments
        $comment = new Comment();
        $pseudo = $app['user'];
        $comment->setBillet($billet);
        $comment->setPseudo($pseudo);
        $comment->setStatus('0');
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);

            $app['session']->getFlashBag()->add('success', 'Votre commentaire a bien été soumis. il est en cours de modération');
             return $app->redirect($app['url_generator']->generate('billet', array('id'=> $id)));
        }
        $commentFormView = $commentForm->createView();

    }
    $comments = $app['dao.comment']->findAllByBillet($id);

    return $app['twig']->render('billet.html.twig', array(
        'billet' => $billet, 
        'comments' => $comments,
        'commentForm' => $commentFormView));
    }
    
    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    /**
     * user details controller.
     *
     * @param integer $id user id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function registerAction(Request $request, Application $app) {
        $user = new User();
        $user->setRole('ROLE_USER');
       
    $userForm = $app['form.factory']->create(UserType::class, $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.bcrypt'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('register_success', 'The user was successfully created. you can connect you now');
        return $app->redirect($app['url_generator']->generate('login'));
    }
    return $app['twig']->render('register.html.twig', array(
        'title' => 'New user',
        'userForm' => $userForm->createView()));
    }
}
