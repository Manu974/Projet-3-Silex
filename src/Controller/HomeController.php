<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Blog\Domain\Comment;
use Blog\Domain\CommentLevel;
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
    public function billetAction($billet_id, Request $request, Application $app) {
        $billet = $app['dao.billet']->find($billet_id);
        $commentFormView = null;

    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        // A user is fully authenticated : he can add comments
        $comment = new Comment();
        $pseudo = $app['user'];

        $comment->setBillet($billet);
        $comment->setPseudo($pseudo);
        $comment->setStatus('0');
        $comment->setReport('1');

        $commentLevel = new CommentLevel();
        $commentLevel->setParent($comment);
        $commentLevel->setLevel(0);

        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
             $app['dao.commentlevel']->save($commentLevel);


            $app['session']->getFlashBag()->add('success', 'Votre commentaire a bien été soumis. il est en cours de modération');
             return $app->redirect($app['url_generator']->generate('billet', array('billet_id'=> $billet_id)));
        }

        $commentFormView = $commentForm->createView();

    }

    $comments = $app['dao.comment']->findAllByBillet($billet_id);
    //$commentsOne = $app['dao.comment']->findAllByBilletCommentLevelOne($billet_id);
    
    


    return $app['twig']->render('billet.html.twig', array(
        'billet' => $billet, 
        'comments' => $comments,
        'commentForm' => $commentFormView
        ));
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

    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function reportAction($comment_id,$billet_id, Request $request, Application $app) {
        $comment = $app['dao.comment']->find($comment_id);
        $comment->setReport('0');
        $app['dao.comment']->update($comment);

         $app['session']->getFlashBag()->add('signalement_success', 'The comment was reported. thanks you');
        return $app->redirect($app['url_generator']->generate('billet', array('billet_id'=> $billet_id)));
    }


   /*
    public function replyAction($comment_id,$billet_id, Request $request, Application $app) {
            $billet = $app['dao.billet']->find($billet_id);
            $comment = $app['dao.comment']->find($comment_id);
            $commentFormViewReply = null;
            $parentComment= $comment->getParent();
            $commentId =$comment->getId();
            $commentLevel = $comment->getLevel();
    
if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $commentReply = new Comment();
            $pseudo = $app['user'];

            if ($parentComment == null) {
                $commentReply->setParent($commentId);
                $commentReply->setLevel('1');
            }

            if ($parentComment == $commentId && $commentLevel == '1' ) {
                $commentReply->setParent($comment->getId());
                $commentReply->setLevel('2');
            }

            if ($parentComment == $commentId && $commentLevel == '2' ) {
                $commentReply->setParent($comment->getId());
                $commentReply->setLevel('3');
            }

        $commentReply->setBillet($billet);
        $commentReply->setPseudo($pseudo);
        $commentReply->setStatus('0');
        $commentReply->setReport('1');

        $commentFormReply = $app['form.factory']->create(CommentType::class, $commentReply);
        $commentFormReply->handleRequest($request);
        
        if ($commentFormReply->isSubmitted() && $commentFormReply->isValid()) {
            $app['dao.comment']->save($commentReply);

            $app['session']->getFlashBag()->add('success_reply', 'Votre commentaire a bien été soumis. il est en cours de modération');
             return $app->redirect($app['url_generator']->generate('billet', array('billet_id'=> $billet_id
                )));
        }

        $commentFormViewReply = $commentFormReply->createView();

        }

    $comments = $app['dao.comment']->findAllByBillet($billet_id);

    return $app['twig']->render('comment_reply.html.twig', array(
        'billet' => $billet, 
        'comments' => $comments,
        'comment' => $comment,

        'commentFormReply' => $commentFormViewReply
        ));

    }*/
        
}
