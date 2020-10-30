<?php

namespace App\Form\Type\Command;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use App\Entity\Command\PieceCommand;
use App\Entity\Prodcut\VariantProduct;



class AddProductCommandType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$builder
			->add('nbProducts', IntegerType::class, 
				['label' => "Nombre de produits en stotck", 'attr' => ['max' => $option['stock'], 'min' => 1]])
			->add('submit', SubmitType::class, ['label' => 'Ajouter', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => PieceCommand::class,
			'stock' => '0'
		]);
	}


}



