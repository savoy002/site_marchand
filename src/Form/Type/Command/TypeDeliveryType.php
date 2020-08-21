<?php

namespace App\Form\Type\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\CompanyDelivery;
use App\Entity\Command\TypeDelivery;

class TypeDeliveryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$builder
			->add('name', TextType::class, ['label' => 'Nom de la livraison', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('price', MoneyType::class, 
				['label' => "Prix du produit", 'divisor' => 100, 'attr' => ['class' => 'form-control form-control-sm']])
			->add('timeMin', IntegerType::class, 
				['label' => 'Nombre de jours minimum', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('timeMax', IntegerType::class, 
				['label' => 'Nombre de jours maximum', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('company', EntityType::class, 
				['label' => 'Entreprise', 'class' => CompanyDelivery::class, 'required' => true, 'choice_label' => 'name', 
				'attr' => ['class' => 'form-control'], 
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('c')->where('c.delete = FALSE');
				}
			])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TypeDelivery::class
		]);
	}

}
