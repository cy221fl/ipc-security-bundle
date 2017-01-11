<?php

namespace IPC\SecurityBundle\Controller;

use IPC\SecurityBundle\Form\Model\ChangePasswordInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PasswordController extends Controller
{

    public function credentialsExpiredAction(Request $request)
    {
        $user    = $request->getSession()->get('credentials_expired_user');
        $model   = $this->container->getParameter('ipc_security.password.credentials_expired.model');
        $form    = $this->container->getParameter('ipc_security.password.credentials_expired.form');
        $view    = $this->container->getParameter('ipc_security.password.credentials_expired.view');
        $options = $this->container->getParameter('ipc_security.password.credentials_expired.options');

        return $this->forward('IPCSecurityBundle:Password:handleChangePassword', [
            'user'        => $user,
            'form'        => $form,
            'formOptions' => $options,
            'view'        => $view,
            'viewOptions' => $options,
            'model'       => $model,
        ]);
    }

    /**
     * Change the user password
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleChangePasswordAction(Request $request)
    {
        // get the parameters form request
        $user         = $request->get('user');
        $modelClass   = $request->get('model');
        $formClass    = $request->get('form');
        $formOptions  = $request->get('formOptions');
        $viewTemplate = $request->get('view');
        $viewOptions  = $request->get('viewOptions');

        /* @var $model ChangePasswordInterface */
        $model    = is_object($modelClass) ? $modelClass : new $modelClass;
        $form     = $this->createForm($formClass, $model, $formOptions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($model->getNew()) {
                $user->setPassword($this
                    ->get('security.encoder_factory')
                    ->getEncoder($user)
                    ->encodePassword($model->getNew(), $user->getSalt())
                );
            }
            $manager = $this->getDoctrine()->getManagerForClass(get_class($user));
            $manager->persist($user);
            $manager->flush();
            // TODO: success message
        } else {
            foreach ($form->getErrors() as $error) {
                // TODO: error messages
            }
        }

        return $this->render($viewTemplate, [
            'password' => $form->createView(),
            'options'  => $viewOptions,
        ]);
    }
}
