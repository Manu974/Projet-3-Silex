<?php

namespace Blog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pseudo', TextType::class);
        $builder->add('content', TextareaType::class);
        $builder->add('dateofpost', DatetimeType::class, array(
        'data' => new \DateTime('now')));
    }

    public function getName()
    {
        return 'comment';
    }
}
