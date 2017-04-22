<?php

namespace IPC\SecurityBundle\Form\Type;

use IPC\SecurityBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{

    /**
     * @var array
     */
    protected $validationGroups = [ 'Default' ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['require_current']) {
            $builder
                ->add(
                    'current',
                    PasswordType::class,
                    [
                        'label'    => 'form.type.change_password.current.label',
                        'required' => false,
                    ]
                );
            $this->validationGroups[] = 'require_current';
        }

        if ($options['require_repeated']) {
            $builder
                ->add(
                    'new',
                    RepeatedType::class,
                    [
                        'type'            => PasswordType::class,
                        'required'        => false,
                        'first_name'      => 'new',
                        'second_name'     => 'repeated',
                        'first_options'   => [
                            'label' => 'form.type.change_password.new.label',
                        ],
                        'second_options'  => [
                            'label' => 'form.type.change_password.repeated.label',
                        ],
                        'invalid_message' => 'form.type.change_password.new.invalid_message',
                    ]
                );
            $this->validationGroups[] = 'require_repeated';
        } else {
            $builder
                ->add(
                    'new',
                    PasswordType::class,
                    [
                        'required' => false,
                        'label'    => 'form.type.change_password.new.label',
                    ]
                );
        }
        $builder
            ->add(
                'update',
                SubmitType::class,
                [
                    'label' => 'common.button.change'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => ChangePassword::class,
                'validation_groups' => function () {
                    // using callback since validation groups where added in buildForm
                    return $this->validationGroups;
                },
            ])
            ->setRequired(['require_current', 'require_repeated'])
        ;
    }
}
