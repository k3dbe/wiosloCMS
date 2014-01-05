<?php

namespace wiosloCMS\UserBundle\Form\Type;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'wiosloCMS\UserBundle\Model\User',
        'name' => 'register',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', ['label' => 'Wybierz nick', 'attr' => ['class' => 'form-control', 'placeholder' => 'nick']]);
        $builder->add('email', 'text', ['label' => 'Podaj email', 'attr' => ['class' => 'form-control', 'placeholder' => 'jan.kowalski@gmail.com']]);
        $builder->add('password', 'repeated',
            [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field form-control')),
                'required' => true,
                'first_options' => array('label' => 'Hasło', 'attr' => ['placeholder' => 'hasło', 'class' => 'password-field form-control']),
                'second_options' => array('label' => 'Powtórz hasło', 'attr' => ['placeholder' => 'powtórz hasło', 'class' => 'password-field form-control']),
            ]
        );
    }

    public function getName()
    {
        return 'register';
    }
}
