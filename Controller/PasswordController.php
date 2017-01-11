<?php

namespace IPC\SecurityBundle\Controller;

use IPC\SecurityBundle\Entity\Role;
use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Form\Model\ChangePassword;
use IPC\SecurityBundle\Form\Type\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PasswordController extends Controller
{

    /**
     * Change the user password
     * @param Request $request
     * @param User    $user
     * @param array   $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Request $request, User $user, array $options)
    {
        $flashBag = $this->get('session')->getFlashBag();
        $model    = new ChangePassword();
        $form     = $this->createForm(ChangePasswordType::class, $model, $options);
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

        return $this->render('@IPCSecurity/Password/change.html.twig', [
            'password' => $form->createView(),
            'options'  => $options,
        ]);
    }

    /**
     * Returns based on the given user and the current user which fields to show
     *
     * @param User $user
     *
     * @return array
     */
    protected function getChangePasswordOptions(User $user)
    {
        $options = [
            'require_current'  => true,
            'require_repeated' => true,
        ];
        /* @var $currentUser User */
        try {
            $currentUser = $this->getUser();
            if ($currentUser->hasRole(Role::ROLE_ADMIN) && $user->getUserId() !== $currentUser->getUserId()) {
                $options['require_current'] = false;
            }
        } catch (\LogicException $e) {
            // do nothing
        }
        return $options;
    }
}