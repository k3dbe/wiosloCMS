<?php

namespace wiosloCMS\HomepageBundle\Controller;

use wiosloCMS\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TopController extends Controller
{
    public function showAction()
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($user instanceof User) {
            return $this->render('HomepageBundle:Top:authenticated.html.twig');
        }

        return $this->render('HomepageBundle:Top:anonymous.html.twig');
    }
}
