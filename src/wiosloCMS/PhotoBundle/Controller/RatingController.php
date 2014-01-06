<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;
use wiosloCMS\PhotoBundle\Model\UserRate;
use wiosloCMS\PhotoBundle\Model\UserRateQuery;

class RatingController extends Controller
{
    public function rateAction($photoId, $action)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if (UserRateQuery::create()->filterByUser($this->getUser())->filterByPhotoId($photoId)->exists()) {
            /** @var Session $session */
            $session = $this->get('session');
            $session->getFlashBag()->add('error',"Już głosowałeś na to zdjęcie");
            return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photoId]));
        }

        $photo = PhotoQuery::create()->findPk($photoId);
        $rating = $photo->getRating();

        if ('plus' === $action) {
            $rating->setPlus($rating->getPlus() + 1);
        } elseif ('minus' === $action) {
            $rating->setMinus($rating->getMinus() - 1);
        } else {
            throw new \RuntimeException('Illegal action');
        }

        $rating->save();

        $userRate = new UserRate();
        $userRate->setUser($this->getUser());
        $userRate->setPhotoId($photoId);
        $userRate->save();

        return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photoId]));
    }
}