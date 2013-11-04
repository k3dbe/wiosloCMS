<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use wiosloCMS\PhotoBundle\Form\Type\PhotoType;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoQuery;

class PhotoController extends Controller
{
    public function addAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            //throw new AccessDeniedException();
        }

        $photo = new Photo();
        $photo->setUser($this->getUser());
        $photoForm = $this->createForm(new PhotoType(), $photo);

        $photoForm->handleRequest($request);

        if ($photoForm->isValid()) {
            $photo->save();
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('PhotoBundle::add.html.twig', ['form' => $photoForm->createView()]);
    }

    public function showAction()
    {
        $photos = PhotoQuery::create()->orderByCreatedAt(\Criteria::DESC)->find();

        return $this->render('PhotoBundle::show.html.twig', ['photos' => $photos]);
    }
}