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

class ManageProductVariantProductType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option) 
	{
		$builder
			->add('product', EntityType::class, 
				['label' => 'Produits', 'class' => Product::class, 'required' => true, 'choice_label' => 'name', 
				'attr' => ['class' => 'form-control'],
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('p')->where('p.delete = FALSE');
				}
			])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => VariantProduct::class]);
	}

}


