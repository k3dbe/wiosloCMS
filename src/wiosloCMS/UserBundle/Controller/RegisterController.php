<?php

namespace wiosloCMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use wiosloCMS\UserBundle\Model\User;
use wiosloCMS\UserBundle\Form\Type\UserType;

class RegisterController extends Controller
{

    /**
     * Register new user
     *
     * @return Response
     */
    public function registerAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {

                /** @var PasswordEncoderInterface $encoder */
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
                $user->save();

                return $this->forward('HomepageBundle:Authenticated:index');
            }
        }

        return $this->render('UserBundle::register.html.twig', array('form' => $form->createView()));
    }

}
