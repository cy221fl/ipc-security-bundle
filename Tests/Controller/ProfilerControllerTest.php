<?php

namespace IPC\SecurityBundle\Tests\Controller;

use IPC\TestBundle\Tests\Controller\AbstractControllerTest;

class ProfilerControllerTest extends AbstractControllerTest
{
    public function testProfilerCookie()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->generateUrl('profiler_status'));
        $this->assertRegExp('~disabled~', $crawler->filter('body')->html());
        $crawler = $this->client->request('GET', $this->generateUrl('profiler_enable'));
        $this->assertRegExp('~enabled~', $crawler->filter('body')->html());
        $crawler = $this->client->request('GET', $this->generateUrl('profiler_disable'));
        $this->assertRegExp('~disabled~', $crawler->filter('body')->html());
    }
}