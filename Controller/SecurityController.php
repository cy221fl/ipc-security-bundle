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
        $viewTemplate       = $this->container->getParameter('ipc_security.login.view');
        $credentialsExpired = $this->container->getParameter('ipc_security.login.credentials_expired');
        $flashBagOptions    = $this->container->getParameter('ipc_security.login.flash_bag');

        $loginForm = $this->createForm($formClass);
        $session   = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $exception = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $exception = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $exception = null;
        }

        // redirect in case of credentials expired is configured
        if ($exception instanceof CredentialsExpiredException && $credentialsExpired) {
            $request->getSession()->set('credentials_expired_user', $exception->getUser());
            return $this->redirectToRoute($credentialsExpired['route']);
        }

        // add a flash message
        if ($exception && $flashBagOptions && $flashBagOptions['type']['error']) {
            $flashBag = $this->get('session')->getFlashBag();
            $type     = $flashBagOptions['type']['error'];
            $flashBag->add(
                $type,
                $this->renderView('@IPCCore/translate.html.twig', ['message' => $exception->getMessage()])
            );
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        return $this->render($viewTemplate, [
            // last username entered by the user
            'last_username' => $lastUsername,
            'exception'     => $exception,
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
            // update credentials
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
            $request->getSession()->remove('credentials_expired_user');

            // add a flash message
            if ($flashBagOptions && $flashBagOptions['type']['success']) {
                $type      = $flashBagOptions['type']['success'];
                $translate = $flashBagOptions['translate'];
                $this->addFlashMessage($type, 'ipc_security.credentials_expired.update_success', $translate);
            }

        } else {
            if ($flashBagOptions && $flashBagOptions['type']['error']) {
                $type      = $flashBagOptions['type']['error'];
                $translate = $flashBagOptions['translate'];
                foreach ($form->getErrors() as $error) {
                    $this->addFlashMessage($type, $error->getMessage(), $translate);
                }
            }
        }

        return $this->render($viewTemplate, [
            'change_password' => $form->createView(),
            'options'         => $options,
        ]);
    }

    /**
     * @param string $type
     * @param string $message
     * @param bool   $translate
     */
    protected function addFlashMessage($type, $message, $translate)
    {
        if ($translate) {
            $this->addFlash($type, $this->renderView('@IPCCore/translate.html.twig', ['message' => $message]));
        } else {
            $this->addFlash($type, $message);
        }
    }
}