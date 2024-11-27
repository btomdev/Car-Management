<?php

namespace App\Form;

use App\Domain\Car\DTO\CarFilterDTO;
use App\Repository\CarRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Marque'],
            ])
            ->add('type', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Type'],
            ])
            ->add('seats', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Nombre de passagers'],
            ])
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Nouveautés' => 'DESC',
                    'les plus anciennes' => 'ASC',
                ],
                'placeholder' => 'Trier par',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarFilterDTO::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
