<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Model\Pet\Enum\Status;

class PetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'available' => Status::AVAILABLE->value,
                    'pending' => Status::PENDING->value,
                    'sold' => Status::SOLD->value,
                ],
            ])
            ->add('save', SubmitType::class);
        ;
    }
}
