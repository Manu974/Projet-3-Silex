<?php

namespace Blog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BilletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class,array('required'    => false))
            ->add('publication', DatetimeType::class, array(
        'data' => new \DateTime('now')))
            ->add('valider',   SubmitType::class);
    }

    public function getName()
    {
        return 'billet';
    }
}