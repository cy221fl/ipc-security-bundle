<?php

namespace Tests\IPC\SecurityBundle\Form\Model;

use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Form\Model\ChangePassword;
use IPC\TestBundle\Tests\AbstractSymfonyTest;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ChangePasswordTest extends AbstractSymfonyTest
{
    public function testModel()
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        $data = [
            'current'  => 'current_pw',
            'new'      => 'new_pw',
            'repeated' => 'repeated_pw',
        ];

        $changePassword = $serializer->denormalize($data, ChangePassword::class);
        $this->assertEquals($data, $serializer->normalize($changePassword));
    }

    public function testValidation()
    {
        $changePassword   = new ChangePassword();
        $validationGroups = ['Default'];

        $violationList = $this->validator->validate($changePassword, null, $validationGroups);
        $this->assertViolationExists('form.model.change_password.new.not_blank', 'new', $violationList);
        $this->assertCount(1, $violationList);

        // prepare constraint dependencies
        $user = new User();
        $user->setPassword('plain_pw');
        $user->setUsername('user');
        $token = new UsernamePasswordToken($user, 'credential', 'provider');
        $tokenStorage = $this->container->get('security.token_storage');
        $tokenStorage->setToken($token);

        $validationGroups[] = 'require_current';

        $violationList = $this->validator->validate($changePassword, null, $validationGroups);
        $this->assertViolationExists('form.model.change_password.current.not_blank', 'current', $violationList);
        $this->assertViolationExists('form.model.change_password.constraints.equal_properties', 'new', $violationList);
        $this->assertViolationExists('form.model.change_password.new.not_blank', 'new', $violationList);
        $this->assertCount(3, $violationList);

        $changePassword->setNew('password');
        $changePassword->setCurrent('password');
        $violationList = $this->validator->validate($changePassword, null, $validationGroups);
        $this->assertViolationExists('form.model.change_password.current.user_password', 'current', $violationList);
        $this->assertViolationExists('form.model.change_password.constraints.equal_properties', 'new', $violationList);
        $this->assertCount(2, $violationList);


    }
}