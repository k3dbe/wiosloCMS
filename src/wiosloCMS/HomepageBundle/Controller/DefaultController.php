<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HomepageBundle::index.html.twig');
    }
}
