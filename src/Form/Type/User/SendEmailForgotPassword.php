<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SendEmailForgotPassword extends AbstractType
{

	public function  buildForm(FormBuilderInterface $builder, array $option)
	{
		$builder
			->add('email', EmailType::class, ['label' => 'Adresse email', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
	}

}
