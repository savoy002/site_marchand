<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeForgotPasswordType extends AbstractType 
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('password', PasswordType::class, 
				['label' => 'Nouveau mot de passe', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('verifyPassword', PasswordType::class, 
				['label' => 'Vérification du nouveau mot de passe', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

}

