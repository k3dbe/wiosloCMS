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
        $builder->add('username', 'text', ['label' => 'Username: ']);
        $builder->add('name', 'text', ['label' => 'Name: ']);
        $builder->add('surname', 'text', ['label' => 'Surname: ']);
        $builder->add('email', 'text', ['label' => 'Email: ']);
        $builder->add('city', 'text', ['label' => 'City: ']);
        $builder->add('birthday', 'birthday', ['format' => 'yyyy-MM-dd', 'label' => 'Birthday: ']);
        $builder->add('gender', 'choice', ['choices' => ['female' => 'Female', 'male' => 'Male'], 'label' => 'Gender: ']);
        $builder->add('password', 'repeated',
            [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options' => array('label' => 'Password: '),
                'second_options' => array('label' => 'Repeat Password: '),
            ]
        );

        $builder->add('Register', 'submit');
    }

    public function getName()
    {
        return 'register';
    }
}
