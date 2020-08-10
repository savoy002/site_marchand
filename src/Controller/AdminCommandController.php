<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Command\Command;

class AdminCommandController extends AbstractController 
{

	//Le nombre de Command par page.
    //Attention si vous changez la valeur de cette constante pensez aussi à changer celle des tests.
	const NUMBER_BY_PAGE = 5;


	/**
	 * @Route("/admin/commands", name="commands")
	 */
	public function commands(Request $request) 
	{
        $former_request = array();
        $errors = array();
        $criteria = ['number_by_page' => self::NUMBER_BY_PAGE];
        //Recherche la demande de page de l'administrateur si elle existe.
        $page = $request->request->get('page');

        //Gestion de la sélection si il y an a une.
        if($request->request->get('research') === "research") {

        	//Création des différents paramètres de la recherche.
        	if($request->request->get('createdBefore') != "" && $request->request->get('createdBefore') !== null 
             &&  $request->request->get('createdAfter') != "" && $request->request->get('createdAfter') !== null) {
                if($request->request->get('createdBefore') >= $request->request->get('createdAfter')) {
                    $criteria['createdBefore'] = $request->request->get('createdBefore');
                    $former_request['createdBefore'] =  $request->request->get('createdBefore');
                    $criteria['createdAfter'] = $request->request->get('createdAfter');
                    $former_request['createdAfter'] =  $request->request->get('createdAfter');
                } else {
                    $errors[] = "La date d'avant la création ne peut pas être inférieur à la date d'après création.";
                }
            } else {
                if($request->request->get('createdBefore') != "" && $request->request->get('createdBefore') !== null) {
                    $criteria['createdBefore'] = $request->request->get('createdBefore');
                    $former_request['createdBefore'] =  $request->request->get('createdBefore');
                }
                if($request->request->get('createdAfter') != "" && $request->request->get('createdAfter') !== null) {
                    $criteria['createdAfter'] = $request->request->get('createdAfter');
                    $former_request['createdAfter'] =  $request->request->get('createdAfter');
                }
            }
            if($request->request->get('sentBefore') != "" && $request->request->get('sentBefore') !== null 
             &&  $request->request->get('sentAfter') != "" && $request->request->get('sentAfter') !== null) {
                if($request->request->get('sentBefore') >= $request->request->get('sentAfter')) {
                    $criteria['sentBefore'] = $request->request->get('sentBefore');
                    $former_request['sentBefore'] =  $request->request->get('sentBefore');
                    $criteria['sentAfter'] = $request->request->get('sentAfter');
                    $former_request['sentAfter'] =  $request->request->get('sentAfter');
                } else {
                    $errors[] = "La date d'avant l'envoie ne peut pas être inférieur à la date d'après l'envoie.";
                }
            } else {
                if($request->request->get('sentBefore') != "" && $request->request->get('sentBefore') !== null) {
                    $criteria['sentBefore'] = $request->request->get('sentBefore');
                    $former_request['sentBefore'] =  $request->request->get('sentBefore');
                }
                if($request->request->get('sentAfter') != "" && $request->request->get('sentAfter') !== null) {
                    $criteria['sentAfter'] = $request->request->get('sentAfter');
                    $former_request['sentAfter'] =  $request->request->get('sentAfter');
                }
            }
            if($request->request->get('receivedBefore') != "" && $request->request->get('receivedBefore') !== null 
             &&  $request->request->get('receivedAfter') != "" && $request->request->get('receivedAfter') !== null) {
                if($request->request->get('receivedBefore') >= $request->request->get('receivedBefore')) {
                    $criteria['receivedBefore'] = $request->request->get('receivedBefore');
                    $former_request['receivedBefore'] =  $request->request->get('receivedBefore');
                    $criteria['receivedAfter'] = $request->request->get('receivedAfter');
                    $former_request['receivedAfter'] =  $request->request->get('receivedAfter');
                } else {
                    $errors[] = "La date d'avant la réception ne peut pas être inférieur à la date d'après la réception.";
                }
            } else {
                if($request->request->get('receivedBefore') != "" && $request->request->get('receivedBefore') !== null) {
                    $criteria['receivedBefore'] = $request->request->get('receivedBefore');
                    $former_request['receivedBefore'] =  $request->request->get('receivedBefore');
                }
                if($request->request->get('receivedAfter') != "" && $request->request->get('receivedAfter') !== null) {
                    $criteria['receivedAfter'] = $request->request->get('receivedAfter');
                    $former_request['receivedAfter'] =  $request->request->get('receivedAfter');
                }
            }

            if($request->request->get('price') != "" && $request->request->get('price') !== null) {
            	$criteria['price'] = array('value' => intval($request->request->get('price') * 100 ), 
                    'type' => $request->request->get('type_research_price'));
                $former_request['price'] = $request->request->get('price');
                $former_request['type_research_price'] = $request->request->get('type_research_price');
            }

            if($request->request->get('adress_value') != "" && $request->request->get('adress_value') !== null) {
                $criteria['adress'] = array('value' => $request->request->get('adress_value'), 
                    'type' => $request->request->get('type_research_adress'));
                $former_request['adress_value'] = $request->requset>get('adress_value');
                $former_request['type_research_adress'] = $request->requset>get('type_research_adress');
            }

            if($request->request->get('status') != "" && $request->request->get('status') !== null) {
                $criteria['status'] = $request->request->get('status');
                $former_request['status'] = $request->request->get('status');
            }

            //Ajout des ordres de recherches.
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
            	$criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }

            $number_commands = intval($this->getDoctrine()->getRepository(Command::class)->adminResearchNumberCommands($criteria)[0][1]);
            $number_pages = 
                intval( $number_commands / self::NUMBER_BY_PAGE ) + ( ( $number_commands % self::NUMBER_BY_PAGE === 0 )?0:1 );

            if($page != "" && $page !== null) {
                if($page === 'Début') {
                    $criteria['page'] = 0;
                    $page = 1;
                } else if($page === 'Fin') {
                    $criteria['page'] = $number_pages - 1;
                    $page = $number_pages;
                } else {
                    $criteria['page'] = intval($page) - 1;
                }
            } else
                $page = 1;
                
            $commands = $this->getDoctrine()->getRepository(Command::class)->adminResearchCommands($criteria);
        } else {
            $criteria['page'] = 0;
            $page = 1;
            $commands = $this->getDoctrine()->getRepository(Command::class)->adminResearchCommands($criteria);
            $number_commands = intval($this->getDoctrine()->getRepository(Command::class)->countNumberCommands()[0][1]);
            $number_pages = 
                    intval( $number_commands / self::NUMBER_BY_PAGE ) + ( ( $number_commands % self::NUMBER_BY_PAGE === 0 )?0:1 );
        }

		return $this->render('admin/commands/commands/commands.html.twig', 
            ['commands' => $commands, 'number_pages' => $number_pages, 'page' => $page, 'request' => $former_request, 
            'errors' => $errors]);
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


