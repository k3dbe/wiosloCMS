<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HeaderController extends Controller
{
    public function topAction()
    {
        return $this->render('HomepageBundle:Header:top.html.twig');
    }

    public function footerAction()
    {
        return $this->render('HomepageBundle:Header:footer.html.twig');
    }
}
