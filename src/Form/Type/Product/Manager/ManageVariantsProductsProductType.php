<?php

namespace App\Form\Type\Product\Manager;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;

class ManageVariantsProductsProductType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option) 
	{
		$builder
			->add('variantsProducts', EntityType::class, 
				['label' => 'Variantes de produits', 'class' => VariantProduct::class, 'required' => false, 'choice_label' => 'name', 
				'multiple' => true, 'expanded' => true,
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('v')->where('v.delete = FALSE');
				}
			])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Product::class]);
	}

}


