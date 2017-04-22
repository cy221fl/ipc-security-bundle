<?php

namespace IPC\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfilerController extends Controller
{

    public function enableAction()
    {
        $response = $this->redirectToRoute('profiler_status');
        $response->headers->setCookie(new Cookie('profiler', true));
        return $response;
    }

    public function disableAction()
    {
        $response = $this->redirectToRoute('profiler_status');
        $response->headers->clearCookie('profiler');
        return $response;
    }

    public function statusAction(Request $request)
    {
        $response = new Response();
        $enabled = (bool) $request->cookies->filter('profiler', false);
        return $this->render(
            'IPCSecurityBundle:Profiler:status.html.twig',
            ['enabled' => $enabled],
            $response
        );
    }
}