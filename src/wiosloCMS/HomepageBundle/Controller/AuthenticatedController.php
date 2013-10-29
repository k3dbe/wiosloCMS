<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Propel\PropelBundle\Tests\Fixtures\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            throw new AccessDeniedException();
        }

        /** @var $user User */
        $user = $this->getUser();
        if (!($user instanceof User)) {
            return $this->forward('HomepageBundle:Anonymous:index');
        }

        return $this->render('HomepageBundle:Authenticated:index.html.twig');
    }
}
