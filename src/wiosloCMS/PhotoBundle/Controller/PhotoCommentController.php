<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use wiosloCMS\PhotoBundle\Form\Type\PhotoCommentType;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoComment;

class PhotoCommentController extends Controller
{
    public function addAction(Request $request, $photoId)
    {
        $comment = new PhotoComment();
        $comment->setUser($this->getUser());
        $comment->setPhotoId($photoId);
        $photoCommentForm = $this->createForm(new PhotoCommentType(), $comment);

        $photoCommentForm->handleRequest($request);

        if ($photoCommentForm->isValid()) {
            if (!$this->get('security.context')->isGranted('ROLE_USER')) {
                /** @var Session $session */
                $session = $this->get('session');
                $session->getFlashBag()->add('error', "Tylko zalogowani użytkownicy mogą dodawać komentarze");
                return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photoId]));
            }

            $comment->save();
            return $this->redirect($this->generateUrl('homepage_photo', ['id' => $photoId]));
        }

        return $this->render('PhotoBundle::addComment.html.twig', ['form' => $photoCommentForm->createView(), 'photoId' => $photoId]);
    }

    public function getListAction(Photo $photo)
    {
        $comments = $photo->getPhotoCommentsJoinUser();

        return $this->render('PhotoBundle::listComments.html.twig', ['comments' => $comments]);
    }
}