<?php

namespace Tests\IPC\SecurityBundle\Controller;

use IPC\SecurityBundle\Entity\User;
use IPC\TestBundle\Tests\Controller\AbstractControllerTest;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class SecurityControllerTest extends AbstractControllerTest
{
    public function setUp()
    {
        parent::setUp();
        $this->buildDatabase();
    }

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

    public function testLoginSuccess()
    {
        $user = $this->createUser('login_success', 'login', ['ROLE_USER'], true, true, true, true);

        $this->client->followRedirects();
        $crawler = $this->client->request('POST', 'login_check', ['login' => ['username' => $user->getUsername(), 'password' => $user->getPassword()]]);
        $url = $this->container->get('router')->generate('profiler_status', [], Router::ABSOLUTE_URL);

        $this->assertEquals($url, $crawler->getUri());
    }

    public function testCredentialsExpired()
    {
        $this->client->followRedirects();

        $user = $this->createUser('login_expired', 'login', ['ROLE_USER'], true, true, false, true);

        // test direct access to credentials_expired
         $this->client->request('GET', 'credentials_expired');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        // test redirect to credentials_expired
        $crawler = $this->client->request('POST', 'login_check', ['login' => ['username' => $user->getUsername(), 'password' => $user->getPassword()]]);
        $url = $this->container->get('router')->generate('credentials_expired', [], Router::ABSOLUTE_URL);
        $this->assertEquals($url, $crawler->getUri());

        // test change_password form
        $form = $crawler->filter('form[name=change_password]');
        $this->assertCount(1, $form);
        $this->assertRegExp('~post~i', $form->attr('method'));

        $this->assertCount(1, $form->filter('label[for=change_password_new_new]'));
        $this->assertCount(1, $form->filter('#change_password_new_new'));

        $this->assertCount(1, $form->filter('label[for=change_password_new_repeated]'));
        $this->assertCount(1, $form->filter('#change_password_new_repeated'));

        $this->assertCount(1, $form->filter('#change_password_update'));

        // test submit invalid change_password form
        $crawler = $this->client->request('POST', 'credentials_expired', ['change_password' => ['new' => ['new' => 'old-password', 'repeated' => 'new-password']]]);
        $this->assertEquals($url, $crawler->getUri());

        // test submit valid change_password form, redirect to defined route
        $crawler = $this->client->request('POST', 'credentials_expired', ['change_password' => ['new' => ['new' => 'new-password', 'repeated' => 'new-password']]]);
        $url = $this->container->get('router')->generate('login', [], Router::ABSOLUTE_URL);
        $this->assertEquals($url, $crawler->getUri());
    }

    protected function buildDatabase()
    {
        $app = new Application(static::$kernel);
        $app->setAutoExit(false);

        $output = new NullOutput();

        $app->run(new ArrayInput([
            'doctrine:database:drop',
            '--force' => true,
        ]), $output);

        $app->run(new ArrayInput([
            'doctrine:database:create',
        ]), $output);

        $app->run(new ArrayInput([
            'doctrine:schema:create',
        ]), $output);
    }

    protected function createUser($username, $password, array $roles = [], $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        // generate
        $userEntity = new User();
        $userEntity->setUsername($username);
        $userEntity->setPassword($password);
        $userEntity->setEnabled($enabled);
        $userEntity->setExpired(!$userNonExpired);
        $userEntity->setCredentialsExpired(!$credentialsNonExpired);
        $userEntity->setLocked(!$userNonLocked);
        foreach ($roles as $role) {
            $userEntity->addRole($role);
        }

        // persist
        $manager = $this->manager->getManagerForClass(User::class);
        $manager->persist($userEntity);
        $manager->flush();
        $manager->refresh($userEntity);

        // prepare cleanup
        $this->removeEntity($userEntity);

        return $userEntity;
    }
}