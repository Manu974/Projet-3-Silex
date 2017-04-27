<?php

namespace Blog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('label'=> "Nom d'utilisateur"))
            ->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                 'invalid_message' => 'les mots de passes ne sont pas indentiques',
                 'options'         => array('required' => true),
                 'first_options'   => array('label' => 'Mot de passe'),
                 'second_options'  => array('label' => 'Repeter le mot de passe'),
             ))
            ->add('role', ChoiceType::class, array(
                'choices' => array('Administrateur' => 'ROLE_ADMIN', 'Utilisateur' => 'ROLE_USER')
            ));
    }

    public function getName()
    {
        return 'user';
    }
}
