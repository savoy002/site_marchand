<?php


namespace App\Form\Type\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Command\Command;
use App\Entity\Command\Delivery;


class AddCommandToDeliveryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $option)
	{
		$id_company = $option['id_company'];
		$builder
			->add('commands', EntityType::class, 
				['label' => 'Choix des commandes', 'class' => Command::class, 'attr' => ['class' => ''],
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
