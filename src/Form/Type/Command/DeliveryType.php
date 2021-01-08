<?php


namespace App\Form\Type\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\Command;
use App\Entity\Command\Delivery;
//use App\Entity\Departments;


class DeliveryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		//$choices = new Departments();
		$id_company = $option['id_company'];
		$builder
			->add('date', DateType::class, 
				['widget' => 'single_text', 'label' => 'Date de livraison', 'attr' => ['class' => 'form-control']])
			/*->add('departments', ChoiceType::class, ['label' => "Choix des départements", 
				'choices' => $choices->getListDepartmentForForm(), 'multiple' => true, 'expanded' => true])*/
			->add('commands', EntityType::class, 
				['label' => 'Choix des commandes', 'class' => Command::class, 'attr' => ['class' => ''],
				'multiple' => true, 'expanded' => true,
				'query_builder' => function(EntityRepository $er) use ($id_company) {
						return $er->adminFindCommandsWithoutDelivery($id_company);
					},
				'choice_label' => function($command) {
			 			return $command->showCommand();
			 		}
				])
			->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Delivery::class,
			'id_company' => null,
		]);
	}

}


