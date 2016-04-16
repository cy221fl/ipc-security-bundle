<?php

namespace IPC\SecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'username',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => true,
                    'label'    => 'password',
                ]
            )
            ->add(
                'remember_me',
                CheckboxType::class,
                [
                    'required' => false,
                    'label'    => 'remember me',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'intention' => 'login_form'
        ]);
    }
}
