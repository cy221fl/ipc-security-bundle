<?php

namespace IPC\SecurityBundle\Controller;

use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Form\Model\ChangePassword;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{

    public function loginAction(Request $request)
    {
        $formClass          = $this->container->getParameter('ipc_security.login.form');
        $viewTemplate       = $this->container->getParameter('ipc_security.authentication.login.view');
        $credentialsExpired = $this->container->getParameter('ipc_security.login.credentials_expired');
        $flashBagOptions    = $this->container->getParameter('ipc_security.login.flash_bag');

        $loginForm = $this->createForm($formClass);
        $session   = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }

        // redirect in case of credentials expired is configured
        if ($error instanceof CredentialsExpiredException && $credentialsExpired) {
            $request->getSession()->set('credentials_expired_user', $error->getUser());
            return $this->redirectToRoute($credentialsExpired['route']);
        }

        // add a flash message
        if ($error && $flashBagOptions && $flashBagOptions['type']['error']) {
            $flashBag = $this->get('session')->getFlashBag();
            $type     = $flashBagOptions['type']['error'];
            $flashBag->add(
                $type,
                $this->renderView('@IPCCore/translate.html.twig', ['message' => $error->getMessage()])
            );
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        return $this->render($viewTemplate, [
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
            'login'         => $loginForm->createView(),
        ]);
    }

    /**
     * Change the user password
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \RuntimeException When no password encoder could be found for the user
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function credentialsExpiredAction(Request $request)
    {
        /* @var $user User */
        $user            = $request->getSession()->get('credentials_expired_user');
        $formClass       = $this->container->getParameter('ipc_security.credentials_expired.form');
        $viewTemplate    = $this->container->getParameter('ipc_security.credentials_expired.view');
        $options         = $this->container->getParameter('ipc_security.credentials_expired.options');
        $flashBagOptions = $this->container->getParameter('ipc_security.credentials_expired.flash_bag');

        $model = new ChangePassword();
        $form  = $this->createForm($formClass, $model, $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setCredentialsExpired(false)
                ->setPassword($this
                    ->get('security.encoder_factory')
                    ->getEncoder($user)
                    ->encodePassword($model->getNew(), $user->getSalt())
                );
            $manager = $this->getDoctrine()->getManagerForClass(get_class($user));
            $manager->persist($user);
            $manager->flush();

            if ($flashBagOptions && $flashBagOptions['type']['success']) {
                $flashBag = $this->get('session')->getFlashBag();
                $type     = $flashBagOptions['type']['success'];
                $flashBag->add(
                    $type,
                    $this->renderView('@IPCCore/translate.html.twig', [
                        'message' => 'ipc_security.credentials_expired.update_success'
                    ])
                );
            }
            $request->getSession()->remove('credentials_expired_user');
        } else {
            if ($flashBagOptions && $flashBagOptions['type']['error']) {
                $flashBag = $this->get('session')->getFlashBag();
                $type     = $flashBagOptions['type']['error'];
                foreach ($form->getErrors() as $error) {
                    $flashBag->add(
                        $type,
                        $this->renderView('@IPCCore/translate.html.twig', [
                            'message' => $error->getMessage()
                        ])
                    );
                }
            }
        }

        return $this->render($viewTemplate, [
            'change_password' => $form->createView(),
            'options'         => $options,
        ]);
    }
}