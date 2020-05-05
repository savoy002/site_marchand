<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

use App\Entity\User\User;

class UploadImageType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('image', FileType::class, ['label' => 'Image', 'required' => true, 'mapped' => true,
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

}
