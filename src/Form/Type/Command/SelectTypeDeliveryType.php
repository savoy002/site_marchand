<?php

namespace App\Form\Type\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\Command;
use App\Entity\Command\TypeDelivery;


class SelectTypeDeliveryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$zip_code = $option['zip_code'];
		$builder
			->add('typeDelSelected', EntityType::class, 
				['label' => 'Moyen de livraison', 'class' => TypeDelivery::class, 'required' => true, 
				 'attr' => ['class' => 'form-control'],
				 'expanded' => true,
				 'query_builder' => function(EntityRepository $er) use ($zip_code) {
						return $er->findFormSelectTypeDelivery($zip_code);
					},
				'choice_label' => function($typeDelivery) {
			 			return $typeDelivery->showTypeDelivery();
			 		},
				])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Command::class,
			'zip_code' => null
		]);
	}

}
