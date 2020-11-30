<?php

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\User\User;


class UserType extends AbstractType 
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', TextType::class, ['label' => "Nom d'utilisateur", 'attr' => ['class' => 'form-control form-control-sm']])
			->add('email', EmailType::class, ['label' => 'Adresse email', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('password', PasswordType::class, ['label' => 'Mot de passe', 'attr' => ['class' => 'form-control form-control-sm']])
			->add('verifyPassword', PasswordType::class, ['label' => 'Vérification de mot de passe', 'mapped' => false, 
				'attr' => ['class' => 'form-control form-control-sm']])
			/*->add('roles', ChoiceType::class, ['label' => "Type d'utilisateur", 
				'choices' => ['Utilisateur' => ['ROLE_USER'], 'Administrateur' => ['ROLE_ADMIN']]])*/
			->add('roles', ChoiceType::class, ['label' => "Type d'utilisateur", 
				'choices' => 
					['Utilisateur' => 'ROLE_USER', 'Administrateur' => 'ROLE_ADMIN', 'Entreprise de livraison' => 'ROLE_COMPANY_ADMIN'],
				'attr' => ['class' => 'custom-select custom-select-sm']])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);

		$builder->get('roles')->addModelTransformer(new CallbackTransformer(
			function ($rolesAsArray) {
				return implode(', ', $rolesAsArray);
			},
			function ($rolesAsString) {
				return explode(', ', $rolesAsString, 1);
			}
		));

	}

	public function configureOptions(OptionsResolver $resolver) 
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}

}
