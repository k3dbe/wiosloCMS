<?php

namespace wiosloCMS\PhotoBundle\Form\Type;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PhotoType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'wiosloCMS\PhotoBundle\Model\Photo',
        'name' => 'photo',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Title: ', 'required' => true]);
        $builder->add('description', 'textarea', ['label' => 'Description: ']);
        $builder->add('uri', 'text', ['label' => 'URL: ']);

        $builder->add('Upload', 'submit');
    }

    public function getName()
    {
        return 'photo';
    }
}
