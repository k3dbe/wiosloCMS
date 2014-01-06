<?php

namespace wiosloCMS\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use wiosloCMS\UserBundle\Model\User;

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

    public function changeTemplateAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $settings = $user->getSettings();

        if ($settings->get('white_tpl', false)) {
            $settings->remove('white_tpl');
            $settings->save();
            return $this->redirect($this->generateUrl('homepage'));
        }

        $settings->set('white_tpl', true);
        $settings->save();
        return $this->redirect($this->generateUrl('homepage'));

    }
}
