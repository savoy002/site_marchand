<?php

namespace App\Controller;

use DateTime;

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



class AdminCommandController extends AbstractController 
{

	//Le nombre de Command par page.
    //Attention si vous changez la valeur de cette constante pensez aussi à changer celle du test.
	const NUMBER_BY_PAGE = 5;

    public function __construct() {
        $this->filesSystem = new Filesystem();
    }

    //
    //Partie Command.
    //

	/**
	 * @Route("/admin/command/commands", name="commands")
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

            //Ajout des ordres de recherches.
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
            	$criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }

            //Calcul le nombre de commandes et de pages.
            $number_commands = intval($this->getDoctrine()->getRepository(Command::class)
                ->adminResearchNumberCommands($criteria)[0][1]);
            $number_pages = 
                intval( $number_commands / self::NUMBER_BY_PAGE ) + 
                ( ( $number_commands % self::NUMBER_BY_PAGE === 0 )?0:1 );

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
            
        } else {
            $page = 1;
            if(!$this->isAdmin()){
                $criteria['company'] = $this->getCompanyId();
                $number_commands = intval($this->getDoctrine()->getRepository(Command::class)
                    ->adminResearchNumberCommands($criteria)[0][1]);
            } else {
                //Calcul le nombre de commandes et de pages.
                $number_commands = intval($this->getDoctrine()->getRepository(Command::class)->countNumberCommands()[0][1]);
            }
            $number_pages = intval( $number_commands / self::NUMBER_BY_PAGE ) + 
                            ( ( $number_commands % self::NUMBER_BY_PAGE === 0 )?0:1 );
        }
        //Recherche les commandes à retourner.
        $commands = $this->getDoctrine()->getRepository(Command::class)->adminResearchCommands($criteria);

		return $this->render('admin/commands/commands/commands.html.twig', 
            ['commands' => $commands, 'number_pages' => $number_pages, 'page' => $page, 'request' => $former_request, 
            'errors' => $errors]);
	}

	/**
	 * @Route("/admin/command/command/{id}", name="command")
	 */
	public function command($id) 
	{
        if($this->isAdmin())
            $command = $this->getDoctrine()->getRepository(Command::class)
                ->findOneBy(['id' => $id, 'delete' => false, 'isBasket' => false]);
        else
            $command = $this->getDoctrine()->getRepository(Command::class)
                ->adminFindCommand($id, $this->getCompanyId());

		if(is_null($command))
			return $this->redirect('commands');

		return $this->render('admin/commands/commands/command.html.twig', ['command' => $command]);
	}

    /**
     * @Route("/admin/command/commands/not_send", name="commands_not_send")
     */
    public function commandsWithoutDelivery()
    {
        if($this->isAdmin())
            $commands = $this->getDoctrine()->getRepository(Command::class)
                ->findBy(['delete' => false, 'delivery' => null, 'isBasket' => false]);
        else
            $commands = $this->getDoctrine()->getRepository(Command::class)
                ->adminFindCommandsWithoutDelivery($this->getCompanyId());

        return $this->render('admin/commands/commands/commands_not_send.html.twig', ['commands' => $commands]);
    }

    //
    //Ensemble Delivery.
    //

    /**
     * @Route("/admin/delivery/menu_deliveries", name="menu_delivery")
     */
    public function menuDeliveries() 
    {
        //Modifier le template pour les administrateurs d'entreprises.
        return $this->render('admin/commands/deliveries/menu_delivery.html.twig', 
            ['admin' => $this->getUser(), 'is_admin' => $this->isAdmin() ]);
    }

    //
    //Partie CompanyDelivery.
    //

    /**
     * @Route("/admin/delivery/companies_deliveries", name="companies_deliveries")
     */
    public function companiesDeliveries() 
    {
        if($this->isAdmin()) {
            $companies = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findBy(['delete' => false]);

            return $this->render('admin/commands/deliveries/companies_deliveries/companies_deliveries.html.twig', 
                ['companies' => $companies]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/company_delivery/{id}", name="company_delivery")
     */
    public function companyDelivery($id)
    {
        if($this->isAdmin() || $this->getCompanyId() == $id) {
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['delete' => false, 'id' => $id]);
            if(is_null($company))
                return $this->redirectToRoute('companies_deliveries');
            $departments = new Departments();
            $listDepartments = $departments->getListDepartment();
            return $this->render('admin/commands/deliveries/companies_deliveries/company_delivery.html.twig', 
                ['company' => $company, 'departments' => $listDepartments, 'is_admin' => $this->isAdmin()]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/companies_deliveries/create", name="create_company_delivery")
     */
    public function createCompanyDelivery(Request $request)
    {
        if($this->isAdmin()) {
            $company = new CompanyDelivery();
            $form = $this->createForm(CompanyDeliveryType::class, $company, ['all_france_value' => 'yes', 'create' => true]);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();
                if($image) {
                    $res = $this->saveImage($image, 'company_delivery_image_directory');
                    if(gettype($res) === "string")
                        $company->setLogoFileName($res);
                    else 
                        return $this->render('admin/commands/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                if($form->get('all_france')->getData() == "yes") {
                    $company->setArea(["All"]);
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('companies_deliveries');
                } else {
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('choice_departement_company_delivery', ['id' => $company->getId()]);
                }
            }
            return $this->render('admin/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors, 'create' => true]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/company_delivery/{id}/modify", name="modify_company_delivery")
     */
    public function modifyCompanyDelivery(Request $request, $id)
    {
        if($this->isAdmin() || $this->getCompanyId() == $id) {
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            if(is_null($company))
                return $this->redirectToRoute('companies_deliveries');
            $option = ['create' => false];
            if(!in_array("All", $company->getArea()))
                $option['all_france_value'] = "no";
            else 
                $option['all_france_value'] = "yes";
            $former_image = $company->getLogoFileName() ;
            $form = $this->createForm(CompanyDeliveryType::class, $company, $option);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();
                if($image) {
                    $res_save = $this->saveImage($image, 'company_delivery_image_directory');
                    if(gettype($res_save) === "string")
                        if($company->getLogoFileName() != null) {
                            $res_delete = $this->deleteImage($company->getLogoFileName(), 'company_delivery_image_directory');
                            if(!$res_delete)
                                $company->setLogoFileName($res_save);
                            else 
                                return $this->render('admin/commands/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
                                    ['form' => $form->createView(), 'errors' => $e[$res_delete], 'create' => true]);
                        } else {
                            $company->setLogoFileName($res_save);
                        }
                    else 
                        return $this->render('admin/commands/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
                            ['form' => $form->createView(), 'errors' => $res, 'create' => true]);
                }
                if($form->get('all_france')->getData() == "yes") {
                    $company->setArea(["All"]);
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('company_delivery', ['id' => $this->getCompanyId()]);
                } else {
                    if(in_array("All", $company->getArea()))
                        $company->setArea([]);
                    $company->setActivate(false);
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('choice_departement_company_delivery', ['id' => $company->getId()]);
                }
            }
            return $this->render('admin/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors, 'create' => false, 'id_company' => $company->getId(), 
                'name' => $company->getName()]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/company_delivery/{id}/choice_departement", name="choice_departement_company_delivery")
     */
    public function choiceDepartementCompanyDelivery(Request $request, $id)
    {
        if($this->isAdmin() || $this->getCompanyId() == $id) {
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            if(is_null($company))
                return $this->redirectToRoute('companies_deliveries');
            $form = $this->createForm(ChoiceDepartmentType::class, $company, ['All' => (in_array("All", $company->getArea()))]);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                //var_dump($form->get('select_all')->getData());
                //die();
                if($form->get('select_all')->getData())
                    $company->setArea(["All"]);
                $this->getDoctrine()->getManager()->persist($company);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('company_delivery', ['id' => $id]);
            }
            return $this->render('admin/commands/deliveries/companies_deliveries/manage_departements.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors, 'company' => $company]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/company_delivery/{id}/activate", name="activate_deactivate_company_delivery")
     */
    public function activateDeactivateCompanyDelivery($id)
    {
        if($this->isAdmin() || $this->getCompanyId() == $id) {
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            if(is_null($company))
                return $this->redirectToRoute('companies_deliveries');
            $company->setActivate(!$company->getActivate());
            //Attention lorsque l'activité d'une entreprise est modifiée ses types sont modifiés avec.
            foreach ($company->getTypes() as $type) {
                $type->setActivate($company->getActivate());
                $this->getDoctrine()->getManager()->persist($type);
            }
            $this->getDoctrine()->getManager()->persist($company);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('company_delivery', ['id' => $id]);
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/company_delivery/{id}/delete", name="delete_company_delivery")
     */
    public function deleteCompanyDelivery($id)
    {
        if($this->isAdmin()) {
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            if(is_null($company))
                return $this->redirectToRoute('companies_deliveries');
            $company->setDelete(true);
            //Attention la suppression d'une entreprise supprime ses types et son administrateur avec.
            foreach ($company->getTypes() as $type) {
                $type->setDelete(true);
                $this->getDoctrine()->getManager()->persist($type);
            }
            $company->getOwner()->setDelete(true);
            $this->getDoctrine()->getManager()->persist($company->getOwner());
            $this->getDoctrine()->getManager()->persist($company);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('companies_deliveries');
        } else 
            return $this->redirectToRoute("menu_delivery");
    }

    //
    //Partie TypeDelivery.
    //

    /**
     * @Route("/admin/delivery/types_deliveries", name="types_deliveries")
     */
    public function typesDeliveries()
    {
        if($this->isAdmin()) {
            $types = $this->getDoctrine()->getRepository(TypeDelivery::class)->findBy(['delete' => false]);
            $company = null;
        } else {
            $types = $this->getDoctrine()->getRepository(TypeDelivery::class)
                ->findBy(['company' => $this->getCompanyId(), 'delete' => false]);
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
        }

        return $this->render('admin/commands/deliveries/types_deliveries/types_deliveries.html.twig', 
            ['types' => $types, 'is_admin' => $this->isAdmin(), 'company' => $company]);
    }

    /**
     * @Route("/admin/delivery/type_delivery/{id}", name="type_delivery")
     */
    public function typeDelivery($id)
    {
        if($this->isAdmin()) {
            $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            $company = null;
        } else {
            $type = $this->getDoctrine()->getRepository(TypeDelivery::class)
                ->findOneBy(['id' => $id, 'company' => $this->getCompanyId(), 'delete' => false]);
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
        }
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        
        return $this->render('admin/commands/deliveries/types_deliveries/type_delivery.html.twig', 
            ['type' => $type, 'is_admin' => $this->isAdmin(), 'company' => $company]);
    }

    /**
     * @Route("/admin/delivery/types_deliveries/create", name="create_type_delivery")
     */
    public function createTypeDelivery(Request $request)
    {
        if(!$this->isAdmin()) {
            $type = new TypeDelivery();
            $form = $this->createForm(TypeDeliveryType::class, $type);
            $form->handleRequest($request);
            $errors = array();
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                if($type->getTimeMin() > $type->getTimeMax()) {
                    $errors[] = "Le temps minimum de livraison ne peut pas être supérieur au temps maximum de livraison.";
                    $valid = false;
                }
                if($type->getPrice() < 0) {
                    $errors[] = "Le prix de livraison ne peut pas être négatif.";
                    $valid = false;
                }
                if($valid) {
                    $type->setCompany($company);
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->persist($type);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('types_deliveries');
                }
            }
            return $this->render('admin/commands/deliveries/types_deliveries/form_type_delivery.html.twig', 
                [ 'form' => $form->createView(), 'errors' => $errors, 'create' => true, 'company' => $company]);
        } else 
            return $this->redirectToRoute('types_deliveries');
    }

    /**
     * @Route("/admin/delivery/type_delivery/{id}/modify", name="modify_type_delivery")
     */
    public function modifyTypeDelivery(Request $request, $id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        if($this->getCompanyId() === $type->getCompany()->getId()) {
            $form = $this->createForm(TypeDeliveryType::class, $type);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                if($type->getTimeMin() > $type->getTimeMax()) {
                    $errors[] = "Le temps minimum de livraison ne peut pas être supérieur au temps maximum de livraison.";
                    $valid = false;
                }
                if($type->getPrice() < 0) {
                    $errors[] = "Le prix de livraison ne peut pas être négatif.";
                    $valid = false;
                }
                if($valid) {
                    $this->getDoctrine()->getManager()->persist($type);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('types_deliveries');
                }
            }
            return $this->render('admin/commands/deliveries/types_deliveries/form_type_delivery.html.twig', 
                [ 'form' => $form->createView(), 'errors' => $errors, 'create' => false, 'company' => $type->getCompany(),
                  'is_admin' => $this->isAdmin(), 'id' => $type->getId(), 'name' => $type->getName()]);
        } else
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/type_delivery/{id}/activate", name="activate_deactivate_type_delivery")
     */
    public function activateDeactivateTypeDelivery($id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            if(is_null($type))
                return $this->redirectToRoute('types_deliveries');
        if($this->getCompanyId() === $type->getCompany()->getId() || $this->isAdmin()) {
            $type->setActivate(!$type->getActivate());
            $this->getDoctrine()->getManager()->persist($type);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('type_delivery', ['id' => $id]);
        } else
            return $this->redirectToRoute("menu_delivery");
    }

    /**
     * @Route("/admin/delivery/type_delivery/{id}/delete", name="delete_type_delivery")
     */
    public function deleteTypeDelivery($id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        if($this->getCompanyId() == $type->getCompany()->getId()) {
            $type->setDelete(true);
            $this->getDoctrine()->getManager()->persist($type);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('types_deliveries');
        } else
            return $this->redirectToRoute("menu_delivery");
    }

    ///**
    // * @Route("admin/delivery/company_delivery/{id}/types_deliveries", name="types_by_company")
    // */
    /*public function typesDeliveriesByCompany($id) 
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');

        return $this->render('admin/commands/deliveries/types_deliveries/types_deliveries_by_company_delivery.html.twig',
            ['company' => $company]);
    }*/

    //
    //Partie Delivery.
    //

    /**
     * @Route("admin/delivery/deliveries", name="deliveries")
     */
    public function deliveries(Request $request) 
    {
        $former_request = array();
        $errors = array();
        $criteria = ['number_by_page' => self::NUMBER_BY_PAGE];
        //Recherche la demande de page de l'administrateur si elle existe.
        $page = $request->request->get('page');

        //Ajout le critère d'entreprise is il n'est pas un administrateur du site ou récupère les entreprises pour la recherche sinon.
        $companies = array();
        if(!$this->isAdmin()){
            $criteria['company'] = $this->getCompanyId();
            $companies = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
        } else 
            $companies = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findBy(['delete' => false]);
            
        //Gestion de la sélection si il y an a une.
        if($request->request->get('research') === "research") {

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

            /*if($request->request->get('type') != "" && $request->request->get('type') !== null) {
                $criteria['type'] = $request->request->get('type');
                $former_request['type'] = $request->request->get('type');
            }*/

            if($request->request->get('company') != "" && $request->request->get('company') !== null) {
                $criteria['company'] = $request->request->get('company');
                $former_request['company'] = $request->request->get('company');
            }

            //Calcul le nombre de livraisons et de pages.
            $number_deliveries = intval($this->getDoctrine()->getRepository(Delivery::class)
                ->companyResearchNumberDeliveries($criteria)[0][1]);
            $number_pages = 
                intval( $number_deliveries / self::NUMBER_BY_PAGE ) + 
                ( ( $number_deliveries % self::NUMBER_BY_PAGE === 0 )?0:1 );

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

        } else {
            $page = 1;
            //Calcul le nombre de commandes et de pages.
            $number_deliveries = intval($this->getDoctrine()->getRepository(Delivery::class)
                ->companyResearchNumberDeliveries($criteria)[0][1]);
            $number_pages = 
                    intval( $number_deliveries / self::NUMBER_BY_PAGE ) + 
                    ( ( $number_deliveries % self::NUMBER_BY_PAGE === 0 )?0:1 );
        }
        //Recherche les livrasons à retourner.
        $deliveries = $this->getDoctrine()->getRepository(Delivery::class)->companyResearchDeliveries($criteria);

        //Recherche les types de livraison pour les recherches.
        /*if($this->isAdmin())
            $types = $this->getDoctrine()->getRepository(TypeDelivery::class)->findBy(['delete' => false]);
        else 
            $types = $this->getDoctrine()->getRepository(TypeDelivery::class)
                ->findBy(['delete' => false, 'company' => $this->getCompanyId()]);*/

        $departments = new Departments();
        $list_departments = $departments->getListDepartment();

        return $this->render('admin/commands/deliveries/deliveries/deliveries.html.twig', 
            ['deliveries' => $deliveries, 'number_pages' => $number_pages, 'page' => $page, 'request' => $former_request, 
             'errors' => $errors, 'companies' => $companies, 'is_admin' => $this->isAdmin(), 
             'departments' => $list_departments]);
    }

    /**
     * @Route("admin/delivery/delivery/{id}", name="delivery")
     */
    public function delivery($id) 
    {
        if($this->isAdmin()) {
            $delivery = $this->getDoctrine()->getRepository(Delivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            $company = null;
        } else {
            $delivery = $this->getDoctrine()->getRepository(Delivery::class)
                ->findOneBy(['id' => $id, 'company' => $this->getCompanyId(), 'delete' => false]);
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
        }

        if(is_null($delivery))
            return $this->redirectToRoute('deliveries');

        $departments = new Departments();
        $list_departments = $departments->getListDepartment();
        
        return $this->render('admin/commands/deliveries/deliveries/delivery.html.twig', 
            ['delivery' => $delivery, 'is_admin' => $this->isAdmin(), 'company' => $company, 'departments' => $list_departments]);
    }

    /**
     * @Route("admin/delivery/delivery/{id}/commands", name="commands_by_delivery")
     */
    public function commandsByDelivery($id)
    {
        if($this->isAdmin()) {
            $delivery = $this->getDoctrine()->getRepository(Delivery::class)->findOneBy(['id' => $id, 'delete' => false]);
            $company = null;
        } else {
            $delivery = $this->getDoctrine()->getRepository(Delivery::class)
                ->findOneBy(['id' => $id, 'delete' => false, 'company' => $this->getCompanyId()]);
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
        }

        if(is_null($delivery))
            return $this->redirectToRoute('deliveries');

        return $this->render('admin/commands/commands/commands_by_delivery.html.twig', 
            ['delivery' => $delivery, 'company' => $company, 'is_admin' => $this->isAdmin()]);
    }

    /**
     * @Route("admin/delivery/create_delivery", name="create_delivery")
     */
    public function createDelivery(Request $request)
    {
        if(!$this->isAdmin()) {
            $delivery = new Delivery();
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
            $errors = array();
            $form = $this->createForm(DeliveryType::class, $delivery);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                if($delivery->getDate() > new DateTime())
                    $errors[] = "La date ne doit pas être postérieur à d'aujourd'hui.";
                if(empty($errors)) {
                    $delivery->setCompany($company);
                    $this->getDoctrine()->getManager()->persist($company);
                    $this->getDoctrine()->getManager()->persist($delivery);
                    foreach ($delivery->getCommands() as $command) {
                        $command->setDelivery($delivery);
                        $this->getDoctrine()->getManager()->persist($command);
                    }
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('deliveries');
                }
            }
            return $this->render('admin/commands/deliveries/deliveries/create_delivery.html.twig', 
                ['form' => $form->createView(), 'company' => $company, 'errors' => $errors]);
        }
        return $this->redirectToRoute('menu_delivery');
    }

    ///**
    // * @Route("admin/delivery/add_command_to_delivery", name="add_command_to_delivery")
    // */
    /*public function choiceCommandToDelivery(Request $request)
    {
        if(!$this->isAdmin()) {
            $delivery = $this->getDoctrine()->getRepository(Delivery::class)
                ->findOneBy(['empty' => true, 'delete' => false, 'company' => $this->getCompanyId()]);;
            if(is_null($delivery))
                return $this->redirectToRoute('create_delivery');
            $form = $this->createForm(AddCommandToDeliveryType::class, $delivery, ['id_company' => $this->getCompanyId()]);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                foreach ($delivery->getCommands() as $command)
                    $this->getDoctrine()->getManager()->persist($command);
                $this->getDoctrine()->getManager()->persist($delivery);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('delivery', ['id' => $delivery->getId()]);
            }
            $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)
                ->findOneBy(['id' => $this->getCompanyId(), 'delete' => false]);
            return $this->render('admin/commands/deliveries/deliveries/add_command_to_delivery.html.twig', 
                ['form' => $form->createView(), 'company' => $company]);
        }
        return $this->redirectToRoute('menu_delivery');
    }*/

    ///**
    // * @Route("admin/delivery/type_delivery/{id}/deliveries", name="deliveries_by_type")
    // */
    /*public function deliveriesByType($id) 
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');

        return $this->render('admin/commands/deliveries/deliveries/deliveries_by_type_delivery.html.twig', 
            ['type' => $type]);
    }*/


    //
    //Les méthodes pour sauvegarder et supprimer les images.
    //

    public function saveImage($image, $parameter_directory) {
        $originalImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeImageName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
        $newImagename = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
        try {
            $image->move($this->getParameter($parameter_directory), $newImagename);
            return $newImagename;
        } catch(FileException $e) {
            return $e;
        }
    }

    public function deleteImage($nameImage, $parameter_directory) {
        /*var_dump($nameImage);
        var_dump($parameter_directory);
        die();*/
        try {
            $this->filesSystem->remove($this->getParameter($parameter_directory).'/'.$nameImage);
            return false;
        } catch( IOExceptionInterface $e) {
            return $e;
        }
    }

    //
    //La méthode indique l'administrateur est un administrateur du site et non d'une entreprise.
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
