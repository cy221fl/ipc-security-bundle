<?php

namespace IPC\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AuthenticationController extends Controller
{
    public function loginAction(Request $request)
    {
        $formClass = $this->container->getParameter('ipc_security.authentication.login.form');
        $loginForm = $this->createForm($formClass);

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        $view = $this->container->getParameter('ipc_security.authentication.login.view');
        return $this->render(
            $view,
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
                'login'         => $loginForm->createView(),
            ]
        );
    }
}