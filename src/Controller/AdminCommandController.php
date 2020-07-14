<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Command\Command;




class AdminCommandController extends AbstractController 
{

	//Le nombre Products ou VariantsProduct par page.
	const NUMBER_BY_PAGE = 5;


	/**
	 * @Route("/admin/commands", name="commands")
	 */
	public function commands(Request $request) 
	{
		$commands = $this->getDoctrine()->getRepository(Command::class)->findBy(['delete' => false], ['createAt' => 'ASC']);

		return $this->render('templates/admin/commands/commands/commmands.html.twig', ['commands' => $commands]);
	}

	/**
	 * @Route("/admin/command/{id}", name="command")
	 */
	public function command($id) 
	{
		$command = $this->getDoctrine()->getRepository(Command::class)->findBy(['delete' => false, 'id' => $id]);
		if(empty($command))
			return $this->redirect('commands');

		return $this->render('templates/admin/commands/commands/commmand.html.twig', ['command' => $command[0]]);
	}


}


