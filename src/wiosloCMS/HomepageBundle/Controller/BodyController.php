<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BodyController extends Controller
{
    public function showAction($id = null, $hasNext = false, $hasPrevious = true)
    {
        return $this->render('HomepageBundle:Body:show.html.twig', ['id' => $id, 'hasNext' => $hasNext, 'hasPrevious' => $hasPrevious]);
    }
}
