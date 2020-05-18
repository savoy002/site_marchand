<?php

namespace App\Form\Type\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use App\Entity\Product\Category;

class CategoryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => 'Nom de la catégorie', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('code', TextType::class, ['label' => 'Code de la catégorie', 'required' => false, 'attr' => ['class' => 'form-control form-control-sm']])
			->add('image', FileType::class, ['label' => 'Image', 'required' => false, 'mapped' => false, 
				'attr' => ['class' => 'form-control form-control-sm'],
				'constraints' => [
					new File(
						['maxSize' => '1024k', 'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
							'mimeTypesMessage' => 'Veillez importer une image de format png.']
					)
				]
			])
			->add('submit', SubmitType::class, ['label' => 'Valider', 
				'attr' => ['class' => 'btn btn-primary']]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Category::class
		]);
	}

}


