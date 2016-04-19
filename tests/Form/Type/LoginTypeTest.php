<?php

namespace Tests\IPC\SecurityBundle\Form\Type;

use IPC\SecurityBundle\Form\Type\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoginTypeTest
 * @package IPC\SecurityBundle\Tests\Form\Type
 *
 * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
 */
class LoginTypeTest extends TypeTestCase
{

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $this->assertFalse($resolver->hasDefault('intention'), 'The Options resolver has an not expected default.');

        $form = new LoginType();
        $form->configureOptions($resolver);

        $this->assertTrue($resolver->hasDefault('intention'), 'The Options resolver has not the expected default.');
    }

    public function testBuildForm()
    {
        $this->assertCount(0, $this->builder, 'The Form builder has already elements.');

        $form = new LoginType();
        $form->buildForm($this->builder, []);

        $this->assertEquals('login_check', $this->builder->getAction());
        $this->assertEquals('POST', $this->builder->getMethod());

        $this->assertCount(4, $this->builder, 'The Form builder has not the expected count of elements.');

        $this->assertTrue($this->builder->has('username'), 'The builder has not the expected element "username"');
        $username = $this->builder->get('username');
        $this->assertTrue($username->getOption('required'));
        $this->assertEquals('username', $username->getOption('label'));

        $this->assertTrue($this->builder->has('password'), 'The builder has not the expected element "password"');
        $password = $this->builder->get('password');
        $this->assertTrue($password->getOption('required'));
        $this->assertEquals('password', $password->getOption('label'));

        $this->assertTrue($this->builder->has('remember_me'), 'The builder has not the expected element "remember_me"');
        $rememberMe = $this->builder->get('remember_me');
        $this->assertFalse($rememberMe->getOption('required'));
        $this->assertEquals('remember me', $rememberMe->getOption('label'));

        $this->assertTrue($this->builder->has('submit'), 'The builder has not the expected element "submit"');
        $this->assertEquals('login', $this->builder->get('submit')->getOption('label'));
    }
}