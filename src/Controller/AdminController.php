<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Blog\Domain\Billet;
use Blog\Domain\User;
use Blog\Form\Type\BilletType;
use Blog\Form\Type\CommentType;
use Blog\Form\Type\UserType;

class AdminController {

    /**
    * Admin home page controller.
    *
    * @param Application $app Silex application
    */
    public function indexAction(Application $app) {
        $billets = $app['dao.billet']->findAll();
        $comments = $app['dao.comment']->findAll();
        $users = $app['dao.user']->findAll();

        $nbUnPublishComments = $app['dao.comment']->findAllUnpublish();
        $nbPublishComments = $app['dao.comment']->findAllpublish();
        $nbReportComments = $app['dao.comment']->findAllreport();

        return $app['twig']->render('admin.html.twig', [
            'billets' => $billets,
            'comments' => $comments,
            'users' => $users,
            'nbUnPublishComments' => $nbUnPublishComments,
            'nbPublishComments' => $nbPublishComments,
            'nbReportComments' => $nbReportComments,
        ]);
    }

    /**
    * Add billet controller.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function addBilletAction(Request $request, Application $app) {
        $billet = new Billet();
        $billetFormView = null;

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $author = $app['user'];
            $billet->setAuthor($author);

            $billetForm = $app['form.factory']->create(BilletType::class, $billet);
            $billetForm->handleRequest($request);

            if ($billetForm->isSubmitted() && $billetForm->isValid()) {
                $app['dao.billet']->save($billet);
                $app['session']->getFlashBag()->add('success', 'Le billet a été publié.');

                return $app->redirect($app['url_generator']->generate('admin_billet_add'));
            }

            $linkFormView = $billetForm->createView();
        }

        return $app['twig']->render('billet_form.html.twig', [
            'title' => 'Nouveau chapitre ?',
            'billetForm' => $billetForm->createView(),
        ]);
    }

    /**
    * Edit billet controller.
    *
    * @param integer $id Billet id
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function editBilletAction($id, Request $request, Application $app) {
        $billet = $app['dao.billet']->find($id);

        $billetForm = $app['form.factory']->create(BilletType::class, $billet);
        $billetForm->handleRequest($request);

        if ($billetForm->isSubmitted() && $billetForm->isValid()) {
            $app['dao.billet']->save($billet);
            $app['session']->getFlashBag()->add('success', 'Le billet a été mise à jour.');

            return $app->redirect($app['url_generator']->generate('admin'));
        }

        return $app['twig']->render('billet_form.html.twig', [
            'title' => "Edition d'un Chapitre",
            'billetForm' => $billetForm->createView(),
        ]);
    }

    /**
    * Delete billet controller.
    *
    * @param integer $id Billet id
    * @param Application $app Silex application
    */
    public function deleteBilletAction($id, Application $app) {
        // Delete all associated comments
        $app['dao.comment']->deleteAllByBillet($id);
        $app['dao.billet']->delete($id);
        $app['session']->getFlashBag()->add('success', 'le billet a été supprimé.');

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
    * Edit comment controller.
    *
    * @param integer $id Comment id
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function editCommentAction($id, Request $request, Application $app) {
        $comment = $app['dao.comment']->find($id);
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'le commentaire a été mise à jour.');

            return $app->redirect($app['url_generator']->generate('admin'));
        }

        return $app['twig']->render('comment_form.html.twig', [
            'title' => "Edition d'un commentaire",
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
    * Edit comment controller.
    *
    * @param integer $id Comment id
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function publishCommentAction($id, Request $request, Application $app) {
        $comment = $app['dao.comment']->find($id);
        $comment->setStatus('1');
        $comment->setReport('1');

        $app['dao.comment']->update($comment);
        $app['session']->getFlashBag()->add('success', 'Le commentaire a été publié.');

        return $app->redirect($app['url_generator']->generate('admin'));
    }


    /**
    * Edit comment controller.
    *
    * @param integer $id Comment id
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function dontpublishCommentAction($id, Request $request, Application $app) {
        $comment = $app['dao.comment']->find($id);
        $comment->setStatus('0');

        $app['dao.comment']->update($comment);

        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
    * Delete comment controller.
    *
    * @param integer $id Comment id
    * @param Application $app Silex application
    */
    public function deleteCommentAction($id, Application $app) {
        $app['dao.commentlevel']->delete($id);
        $app['dao.comment']->delete($id);

        $app['session']->getFlashBag()->add('success', 'Le commentaire a été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
    * Add user controller.
    *
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function addUserAction(Request $request, Application $app) {
        $user = new User();
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
            $app['session']->getFlashBag()->add('success', "L'utilisateur a été crée.");

            return $app->redirect($app['url_generator']->generate('admin_user_add'));
        }

        return $app['twig']->render('user_form.html.twig', [
            'title' => 'Nouvel Utilisateur',
            'userForm' => $userForm->createView(),
        ]);
    }

    /**
    * Edit user controller.
    *
    * @param integer $id User id
    * @param Request $request Incoming request
    * @param Application $app Silex application
    */
    public function editUserAction($id, Request $request, Application $app) {
        $user = $app['dao.user']->find($id);

        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', "L'utilisateur a été mise à jour.");

            return $app->redirect($app['url_generator']->generate('admin'));
        }

        return $app['twig']->render('user_form.html.twig', [
            'title' => "Edition d'un utilisateur",
            'userForm' => $userForm->createView(),
        ]);
    }

    /**
    * Delete user controller.
    *
    * @param integer $id User id
    * @param Application $app Silex application
    */
    public function deleteUserAction($id, Application $app) {
        $app['dao.comment']->deleteAllByUser($id);
        $app['dao.billet']->deleteAllByUser($id);
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', "L'utilisateur a été supprimé.");
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
