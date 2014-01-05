<?php

namespace wiosloCMS\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use wiosloCMS\PhotoBundle\Form\Type\PhotoCommentType;
use wiosloCMS\PhotoBundle\Model\Photo;
use wiosloCMS\PhotoBundle\Model\PhotoComment;

class PhotoCommentController extends Controller
{
    public function addAction(Request $request, $photoId)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            //    throw new AccessDeniedException();
        }

        $comment = new PhotoComment();
        $comment->setUser($this->getUser());
        $comment->setPhotoId($photoId);
        $photoCommentForm = $this->createForm(new PhotoCommentType(), $comment);

        $photoCommentForm->handleRequest($request);

        if ($photoCommentForm->isValid()) {
            $comment->save();
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('PhotoBundle::addComment.html.twig', ['form' => $photoCommentForm->createView(), 'photoId' => $photoId]);
    }

    public function getListAction(Photo $photo)
    {
        $comments = $photo->getPhotoCommentsJoinUser();

        return $this->render('PhotoBundle::listComments.html.twig', ['comments' => $comments]);
    }
}