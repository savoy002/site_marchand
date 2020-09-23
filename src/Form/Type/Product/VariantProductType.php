<?php

namespace App\Form\Type\Product;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use App\Entity\Product\Product;
use App\Entity\Product\VariantProduct;


class VariantProductType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$builder
			->add('name', TextType::class, 
				['label' => "Nom du produit", 'attr' => ['class' => 'form-control form-control-sm']])
			->add('code', TextType::class, 
				['label' => "Code du produit", 'required' => false, 'attr' => ['class' => 'form-control form-control-sm']])
			->add('description', TextareaType::class, 
				['label' => "Description du produit", 'required' => false, 
				'attr' => ['class' => 'form-control form-control-sm']])
			->add('price', MoneyType::class, 
				['label' => "Prix du produit", 'divisor' => 100, 'attr' => ['class' => 'form-control form-control-sm']])
			->add('stock', IntegerType::class, 
				['label' => "Nombre de produits en stotck", 'attr' => ['class' => 'form-control form-control-sm']])
			->add('isWellcome', ChoiceType::class, 
				['label' => "Affiché en page d'accueil", 'choices' => ['Non' => false, 'Oui' => true], 
				'attr' => ['class' => 'form-control form-control-sm']])
			->add('image', FileType::class, 
				['label' => "Image", 'required' => false, 'mapped' => false, 
				'attr' => ['class' => 'form-control form-control-sm'],
				'constraints' => [
					new File(
						['maxSize' => '1024k', 'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
							'mimeTypesMessage' => 'Veillez importer une image de format png, jpeg ou jpg.']
					)
				]
			])
			->add('product', EntityType::class, 
				['label' => "Produit associé", 'class' => Product::class, 'required' => true, 'choice_label' => 'name', 
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



