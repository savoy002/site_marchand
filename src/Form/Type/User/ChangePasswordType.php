<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use App\Entity\User\User;

class ChangePasswordType extends AbstractType 
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('formerPassword', PasswordType::class, 
				['label' => 'Ancien mot de passe', 'attr' => ['class' => 'form-control form-control-sm']/*, 'mapped' => false*/])
			->add('password', PasswordType::class, 
				['label' => 'Nouveau mot de passe', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('verifyPassword', PasswordType::class, 
				['label' => 'Vérification du nouveau mot de passe', 'attr' => ['class' => 'form-control form-control-sm']
				/*, 'mapped' => false*/])
			->add('submit', SubmitType::class, ['label' => 'Valider', 
				'attr' => ['class' => 'btn btn-primary']]);
	}

	/*public function configureOptions(OptionsResolver $resolver) 
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}*/

}
