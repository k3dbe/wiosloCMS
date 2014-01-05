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
        $builder->add('name', 'text', ['label' => 'Title: ', 'required' => true, 'attr' => ['class' => "form-control", 'placeholder' => "Tytuł zdjęcia"]]);
        $builder->add('uri', 'text', ['label' => 'URL: ', 'attr' => ['class' => "form-control", 'placeholder' => "Ścieżka"]]);
    }

    public function getName()
    {
        return 'photo';
    }
}
