<?php

namespace App\Controller;

use DateTime;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Command\Command;
use App\Entity\Command\CompanyDelivery;
use App\Entity\Command\TypeDelivery;
use App\Entity\Command\Delivery;
use App\Entity\Departments;
use App\Form\Type\Command\AddCommandToDeliveryType;
use App\Form\Type\Command\ChoiceDepartmentType;
use App\Form\Type\Command\CompanyDeliveryType;
use App\Form\Type\Command\DeliveryType;
use App\Form\Type\Command\TypeDeliveryType;

#version 6
use Doctrine\Persistence\ManagerRegistry;

class AdminCommandController extends AbstractController 
{

	//Le nombre de Command par page.
    //Attention si vous changez la valeur de cette constante pensez aussi à changer celle du test.
	const NUMBER_BY_PAGE = 5;

    //
    //Partie Command.
    //

	/**
	 * @Route("commands", name="commands")
	 */
    public function commands(Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator) 
    {
        $former_request = array();
        $errors = array();
        $criteria = [];
        $page = $request->query->get('page', '1');

        if(!$this->isAdmin()) {
            $criteria['company'] = $this->getUser()->getCompanyDelivery()->getId();
        }
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

        if($request->request->get('address_value') != "" && $request->request->get('address_value') !== null) {
            $criteria['address'] = array('value' => $request->request->get('address_value'), 
                'type' => $request->request->get('type_research_address'));
            $former_request['address_value'] = $request->request->get('address_value');
            $former_request['type_research_address'] = $request->request->get('type_research_address');
        }

        if($request->request->get('status') != "" && $request->request->get('status') !== null) {
            $criteria['status'] = $request->request->get('status');
            $former_request['status'] = $request->request->get('status');
        }

        //remplacer avec la pagination.
        if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
            $criteria['orderBy'] = 
                array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
            $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
            $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
        }

        $commands = $paginator->paginate($doctrine->getRepository(Command::class)->adminResearchCommands($criteria), 
            $page, self::NUMBER_BY_PAGE);

        $new_page = intval($page) - 1;
        while($commands->count() <= 0 && $new_page > 0) {
            $commands = $paginator->paginate($doctrine->getRepository(Command::class)->adminResearchCommands($criteria), 
                $new_page, self::NUMBER_BY_PAGE);
            $new_page = intval($new_page) - 1;
        }

        return $this->render('commands/commands.html.twig', 
            ['commands' => $commands, /*'number_pages' => $number_pages, 'page' => $page,*/ 'request' => $former_request, 
            'errors' => $errors]);
    }
	
	/**
	 * @Route("command/{id}", name="command")
	 */
	public function command($id, ManagerRegistry $doctrine) 
	{
        if($this->isAdmin())
            $command = $doctrine->getRepository(Command::class)
                ->findOneBy(['id' => $id, 'delete' => false, 'isBasket' => false]);
        else
            $command = $doctrine->getRepository(Command::class)
                ->adminFindCommand($id, $this->getCompanyId());

		if(is_null($command))
			return $this->redirect('commands');

		return $this->render('commands/command.html.twig', ['command' => $command, "isAdmin" => $this->isAdmin()]);
	}

    /**
     * @Route("commands/not_send", name="commands_not_send")
     */
    public function commandsWithoutDelivery(Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator)
    {
        $page = $request->query->get('page', '1');
        if($this->isAdmin()) {
            $commands = $paginator->paginate($doctrine->getRepository(Command::class)
                ->findBy(['delete' => false, 'delivery' => null, 'isBasket' => false]), $page, self::NUMBER_BY_PAGE);
        } else {
            $commands = $paginator->paginate($doctrine->getRepository(Command::class)->adminFindCommandsWithoutDelivery($this->getCompanyId()), 
                $page, self::NUMBER_BY_PAGE);
        }

        return $this->render('commands/commands_not_send.html.twig', ['commands' => $commands]);
    }

    //
    //La méthode indique si l'utilisateur est un administrateur du site.
    //
    protected function isAdmin(){
        return $this->getUser()->getRoles() === ["ROLE_ADMIN"];
    }

    //
    //La méthode pour récupérer l'identifiant de l'entreprise de livraison à partir l'administrateur de l'entreprise.
    //
    protected function getCompanyId(){
        return 
            (!is_null($this->getUser()->getCompanyDelivery()) && $this->getUser()->getRoles() === ["ROLE_COMPANY_ADMIN"])
             ? $this->getUser()->getCompanyDelivery()->getId()
             : null;
    }

}
