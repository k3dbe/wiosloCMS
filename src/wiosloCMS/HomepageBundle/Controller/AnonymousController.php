<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnonymousController extends Controller
{

    public function indexAction()
    {
        return $this->render('HomepageBundle:Anonymous:index.html.twig');
    }
}