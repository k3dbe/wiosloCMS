<?php

namespace wiosloCMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use wiosloCMS\UserBundle\Model\User;
use wiosloCMS\UserBundle\Form\Type\UserType;

class LoginController extends Controller
{

    /**
     * Log in user
     *
     * @return Response
     */
    public function loginAction()
    {
        return $this->render('UserBundle::login.html.twig');
    }
}