<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                        'min' => 6,
                        'minMessage' => 'Lucas est interdit comme nom, minimum {{ limit }}',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'maxMessage'=>'Anticonstitutionnellement est interdit comme nom, maximum {{ limit }}'
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Stp oublie pas le pass frere ...',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Ton mots de passe doit etre d\'au moins {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 15,
                        'maxMessage' => 'Ton mots de passe doit faire moins de {{ limit }} characters'
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
                        'minMessage' => 'Merci de donner une address de minimum {{ limit }} caracteres',
                        'max' => 4096,
                        'maxMessage'=> 'Merci de donner une address de maximum {{ limit }} caracteres'
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
                        'minMessage' => 'Merci de donner une address mail de minimum {{ limit }} caracteres',
                        'max'=>30,
                        'maxMessage' => 'Merci de donner une address mail de maximum {{ limit }} caracteres',
                    ])
                ],
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
