<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use wiosloCMS\PhotoBundle\Form\Type\PhotoType;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;

class PhotoController extends Controller
{
    public function addAction(Request $request)
    {
        $type = ['jpg', 'jpeg', 'png'];

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $photo = new Photo();
        $photo->setUser($this->getUser());
        $photoForm = $this->createForm(new PhotoType(), $photo);

        $photoForm->handleRequest($request);

        if ($photoForm->isValid()) {
            $extension = explode(".", $photo->getUri());
            if (!is_array($extension) || count($extension) < 1 || !(in_array(array_pop($extension), $type))) {
                /** @var Session $session */
                $session = $this->get('session');
                $session->getFlashBag()->add('error', "Zły format zdjęcia");
                return $this->redirect($this->generateUrl('homepage'));
            }

            $photo->save();
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('PhotoBundle::add.html.twig', ['form' => $photoForm->createView()]);
    }

    public function deleteAction(Photo $photo)
    {
        /** @var Session $session */
        $session = $this->get('session');
        if ($photo->getUser() !== $this->getUser() && !$this->getUser()->hasRole('admin')) {
            $session->getFlashBag()->add('error', "Nie możesz usunąć tego zdjęcia");
            return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photo->getId()]));
        }

        $photo->delete();
        $session->getFlashBag()->add('error', "Zdjęcie zostało usunięte");
        return $this->redirect($this->generateUrl('homepage'));
    }

    public function showAction(Photo $photo = null)
    {
        if (!$photo instanceof Photo) {
            $photo = PhotoQuery::create()->orderById(\Criteria::DESC)->limit(1)->findOne();
        }

        if (!$photo instanceof Photo) {
            return new Response();
        }

        $hasNext = PhotoQuery::create()->filterById($photo->getId(), \Criteria::LESS_THAN)->exists();
        $hasPrevious = PhotoQuery::create()->filterById($photo->getId(), \Criteria::GREATER_THAN)->exists();

        return $this->render('PhotoBundle::show.html.twig', ['photo' => $photo, 'hasNext' => $hasNext, 'hasPrevious' => $hasPrevious]);
    }

    public function nextAction(Photo $photo)
    {
        $nextPhoto = PhotoQuery::create()->filterById($photo->getId(), \Criteria::LESS_THAN)->orderById(\Criteria::DESC)->findOne();

        return $this->redirect($this->generateUrl('homepage_photo', ['id' => $nextPhoto->getId()]));
    }

    public function previousAction(Photo $photo)
    {
        $previousPhoto = PhotoQuery::create()->filterById($photo->getId(), \Criteria::GREATER_THAN)->orderById()->findOne();

        return $this->redirect($this->generateUrl('homepage_photo', ['id' => $previousPhoto->getId()]));
    }

    public function randomAction()
    {
        $photo = PhotoQuery::create()->addAscendingOrderByColumn('rand()')->findOne();

        return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photo->getId()]));
    }

    public function searchAction()
    {
        $phrase = $this->getRequest()->get('phrase');
        $photo = PhotoQuery::create()->findOneByName('%' . $phrase . '%');

        if (!$photo instanceof Photo) {
            /** @var Session $session */
            $session = $this->get('session');
            $session->getFlashBag()->add('error', "Zdjęcie nie zostało znalezione");
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photo->getId()]));
    }
}