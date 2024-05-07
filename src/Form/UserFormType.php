<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'TON NOM',
                    ]),
                    new Length([
                        'min' => 4,
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'minMessage'=>"Données username erronees {{ limit }} minimum",
                        'maxMessage'=>"Données username erronees {{ limit }} maximum"

                    ]),
                ],
            ])->add('lastname', TextType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'TON prenom',
                    ]),
                    new Length([
                        'min' => 4,
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'minMessage'=>"Données lastname erronees {{ limit }} minimum",
                        'maxMessage'=>"Données lastname erronees {{ limit }} maximum"
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Stp oublie pas le pass frere ...',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage'=>"Données password erronees {{ limit }} minimum",
                        'maxMessage'=>"Données password erronees {{ limit }} maximum"
                    ]),
                ],
                ])
            ->add('address', TextareaType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Et on te l\'envoie ou ?',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 100,
                        'minMessage'=>"Données address erronees {{ limit }} minimum",
                        'maxMessage'=>"Données address erronees {{ limit }} maximum"
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Et on te l\'envoie ou ?',
                    ]),
                    new Length([
                        'min'=>6,
                        'max'=>30,
                        'minMessage'=>"Données email erronees {{ limit }} minimum",
                        'maxMessage'=>"Données email erronees {{ limit }} maximum"
                    ])
                ],
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => false,
            ])
            ->add('birthdate', DateType::class)
            ->add('submit', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
