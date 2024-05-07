<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ton nom?',
                    ]),
                    new Length([
                        'min'=>6,
                        'minMessage' => 'Minimum {{ limit }} caracteres',
                        'max'=>30,
                        'maxMessage' => 'Maximum {{ limit }} caracteres',
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ta desc?',
                    ]),
                    new Length([
                        'min'=>6,
                        'minMessage' => 'Minimum {{ limit }} caracteres',
                        'max'=>100,
                        'maxMessage' => 'Maximum {{ limit }} caracteres',
                    ])
                ],
            ])
            ->add('price', MoneyType::class, [
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Donne un prix'
                    ]),
                    new Range([
                        'min'=>0,
                        'max'=>100,
                        'notInRangeMessage'=>"Données price erronees",
                    ])
                ]
            ])
            ->add('number', IntegerType::class, [
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Donne une value'
                    ]),
                    new Range([
                        'min'=>0,
                        'max'=>100,
                        'notInRangeMessage'=>"Données number erronees",
                    ])
                ]
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('send', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
