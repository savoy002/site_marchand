<?php

namespace App\Form\Type\Product\Manager;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Product\Category;
use App\Entity\Product\Product;

class ManageCategoriesProductType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option) 
	{
		$builder
			->add('categories', EntityType::class, 
				['label' => 'Catégories', 'class' => Category::class, 'required' => false, 'choice_label' => 'name', 'multiple' => true, 
					'expanded' => true])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Product::class]);
	}

}


