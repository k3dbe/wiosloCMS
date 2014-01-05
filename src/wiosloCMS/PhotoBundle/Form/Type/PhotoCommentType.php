<?php

namespace wiosloCMS\PhotoBundle\Form\Type;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PhotoCommentType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'wiosloCMS\PhotoBundle\Model\PhotoComment',
        'name' => 'photoComment',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', ['attr' => ['class' => "form-control", 'placeholder' => "Twój komentarz...", 'rows' => "4"]]);
    }

    public function getName()
    {
        return 'photoComment';
    }
}
