<?php

namespace App\Form\Type\Command;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\CompanyDelivery;
use App\Entity\Departments;


class ChoiceDepartmentType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$choices = new Departments();
		if($option['All'])
			$builder
				->add('select_all', CheckboxType::class, 
					['label' => "Tous sélectionner", "mapped" => false, 'required' => false, 
					 'attr' => [ 'checked' => 'checked' ] ]);
		else 
			$builder
				->add('select_all', CheckboxType::class, 
					['label' => "Tous sélectionner", "mapped" => false, 'required' => false ]);
		$builder
			->add('area', ChoiceType::class, 
				['label' => "Choix des départements", 'choices' => $choices->getListDepartmentForForm(), 'multiple' => true, 
				 'expanded' => true ])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => CompanyDelivery::class,
			'All' => false
		]);
	}

}
