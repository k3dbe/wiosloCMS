<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BodyController extends Controller
{
    public function showAction($id = null, $hasNext = false, $hasPrevious = true)
    {
        return $this->render('HomepageBundle:Body:show.html.twig', ['id' => $id, 'hasNext' => $hasNext, 'hasPrevious' => $hasPrevious]);
    }

    public function aboutUsAction()
    {
        return $this->render('HomepageBundle:Body:aboutUs.html.twig');
    }

    public function regulationsAction()
    {
        return $this->render('HomepageBundle:Body:regulations.html.twig');
    }
}
