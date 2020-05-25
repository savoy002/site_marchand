<?php

namespace App\Form\Type\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use App\Entity\Product\Product;

class ProductType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom du produit", 'attr' => ['class' => 'form-control form-control-sm']])
			->add('code', TextType::class, ['label' => "Code du produit", 'required' => false, 'attr' => ['class' => 'form-control form-control-sm']])
			->add('description', TextareaType::class, ['label' => "Description du produit", 'required' => false, 
				'attr' => ['class' => 'form-control form-control-sm']])
			->add('image', FileType::class, ['label' => "Image", 'required' => false, 'mapped' => false,
				'attr' => ['class' => 'form-control form-control-sm'],
				'constraints' => [
					new File(
						['maxSize' => '1024k', 'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
							'mimeTypesMessage' => 'Veillez importer une image de format png, jpeg ou jpg.']
					)
				]
			])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Product::class
		]);
	}

}


