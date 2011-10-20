<?php

namespace SfDay\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('publishedAt')
        ;
    }

    public function getName()
    {
        return 'sfday_blogbundle_posttype';
    }
}
