<?php

namespace App\Form\Type\Command;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\Address;

class ChangeAddressType extends AbstractType 
{

	public function buildForm(FormBuilderInterface $builder, array $option) 
	{
		$builder
			->add('street', TextType::class, ['label' => 'Rue', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('zipCode', TextType::class, 
				['label' => 'Code postale', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('city', TextType::class, ['label' => 'Ville', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Address::class]);
	}

}
