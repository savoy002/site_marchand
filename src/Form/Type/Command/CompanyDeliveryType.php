<?php

namespace App\Form\Type\Command;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use App\Entity\Command\CompanyDelivery;

class CompanyDeliveryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		if($option['all_france_value'] === 'yes')
			$choices = ['Oui' => 'yes', 'Non' => 'no'];
		else
			$choices = ['Non' => 'no', 'Oui' => 'yes'];
		$builder
			->add('name', TextType::class, ['label' => "Nom de l'entreprise", 'attr' => ['class' => 'form-control form-control-sm']])
			->add('image', FileType::class, ['label' => 'Image', 'required' => false, 'mapped' => false, 
				'attr' => ['class' => 'form-control form-control-sm'],
				'constraints' => [
					new File(
						['maxSize' => '1024k', 'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
							'mimeTypesMessage' => 'Veillez importer une image de format png, jpeg ou jpg.']
					)
				]
			])
			->add('all_france', ChoiceType::class, ['label' => 'Livraison sur toute la France', 'mapped' => false, 
				'choices' => $choices, 'attr' => ['class' => 'custom-select custom-select-sm']])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => CompanyDelivery::class,
			'all_france_value' => 'yes'
		]);
	}

}


