<?php

namespace wiosloCMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use wiosloCMS\UserBundle\Model\User;
use wiosloCMS\UserBundle\Form\Type\UserType;

class RegisterController extends Controller
{
    /**
     * Register new user
     *
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect('HomepageBundle:Body:show');
        }

        $user = new User();
        $userForm = $this->createForm(new UserType(), $user);

        $userForm->handleRequest($request);

        if ($userForm->isValid()) {

            /** @var PasswordEncoderInterface $encoder */
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
            $user->save();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->container->get('security.context')->setToken($token);

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('UserBundle::register.html.twig', array('form' => $userForm->createView()));
    }
}