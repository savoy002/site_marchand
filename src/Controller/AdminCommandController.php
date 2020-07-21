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
		$commands = $this->getDoctrine()->getRepository(Command::class)->findBy([], ['createdAt' => 'ASC']);

		return $this->render('admin/commands/commands/commands.html.twig', ['commands' => $commands]);
	}

	/**
	 * @Route("/admin/command/{id}", name="command")
	 */
	public function command($id) 
	{
		$command = $this->getDoctrine()->getRepository(Command::class)->findBy(['id' => $id]);
		if(empty($command))
			return $this->redirect('commands');

		return $this->render('admin/commands/commands/command.html.twig', ['command' => $command[0]]);
	}


}


