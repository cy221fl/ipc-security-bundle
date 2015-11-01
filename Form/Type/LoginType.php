<?php

namespace IPC\SecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                [
                    'required' => true,
                    'label'    => 'username',
                ]
            )
            ->add(
                'password',
                'password',
                [
                    'required' => true,
                    'label'    => 'password',
                ]
            )
            ->add(
                'remember_me',
                'checkbox',
                [
                    'required' => false,
                    'label'    => 'remember me',
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label'    => 'login',
                ]
            )
            ->setAction('login_check')
            ->setMethod('POST')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'login';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'intention' => 'login_form'
        ]);
    }
}
