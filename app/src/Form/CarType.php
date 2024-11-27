<?php

namespace App\Form;

use App\Domain\Car\Enum\Type;
use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class)
            ->add('type', TextType::class)
            ->add('seats', IntegerType::class)
            ->add('color', TextType::class);

        if ($options['type'] === Type::utilitaire->name) {
            $builder->add('ptra');
        }

        $builder->setAction($options['action']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
            'type' => null,
        ]);
    }
}
