<?php

namespace IPC\SecurityBundle\Tests\Controller;

use IPC\TestBundle\Tests\Controller\AbstractControllerTest;

class AuthenticationControllerTest extends AbstractControllerTest
{

    public function testLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form[name=login]');
        $this->assertCount(1, $form);
        $this->assertRegExp('~post~i', $form->attr('method'));
        $this->assertEquals('login_check', $form->attr('action'));

        $this->assertCount(1, $form->filter('label[for=login_username]'));
        $this->assertCount(1, $form->filter('#login_username'));

        $this->assertCount(1, $form->filter('label[for=login_password]'));
        $this->assertCount(1, $form->filter('#login_password'));

        $this->assertCount(1, $form->filter('label[for=login_remember_me]'));
        $this->assertCount(1, $form->filter('#login_remember_me'));

        $this->assertCount(1, $form->filter('#login_submit'));
    }

    public function testLoginFailed()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('POST', 'login_check');
        $this->assertRegExp('~/login$~', $this->client->getHistory()->current()->getUri());
        $this->assertRegExp('/Bad credentials/', $crawler->filter('form[name=login]')->html());
    }
}