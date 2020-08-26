<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Command\Command;
use App\Entity\Command\CompanyDelivery;
use App\Entity\Command\TypeDelivery;
use App\Entity\Command\Delivery;
use App\Entity\Departments;
use App\Form\Type\Command\ChoiceDepartmentType;
use App\Form\Type\Command\CompanyDeliveryType;
use App\Form\Type\Command\TypeDeliveryType;



class AdminCommandController extends AbstractController 
{

	//Le nombre de Command par page.
    //Attention si vous changez la valeur de cette constante pensez aussi à changer celle des tests.
	const NUMBER_BY_PAGE = 5;

    //
    //Partie Command.
    //

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

    //
    //Ensemble Delivery.
    //

    /**
     * @Route("/admin/menu_deliveries", name="menu_delivey")
     */
    public function menuDeliveries() 
    {
        return $this->render('admin/commands/deliveries/menu_delivery.html.twig');
    }

    //
    //Partie CompanyDelivery.
    //

    /**
     * @Route("/admin/companies_deliveries", name="companies_deliveries")
     */
    public function companiesDeliveries() 
    {
        $companies = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findBy(['delete' => false]);

        return $this->render('admin/commands/deliveries/companies_deliveries/companies_deliveries.html.twig', 
            ['companies' => $companies]);
    }

    /**
     * @Route("/admin/company_delivery/{id}", name="company_delivery")
     */
    public function companyDelivery($id)
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findBy(['delete' => false, 'id' => $id]);
        if(empty($company))
            return $this->redirectToRoute('companies_deliveries');
        $departments = new Departments();
        $listDepartments = $departments->getListDepartment();
        return $this->render('admin/commands/deliveries/companies_deliveries/company_delivery.html.twig', 
            ['company' => $company[0], 'departments' => $listDepartments]);
    }

    /**
     * @Route("/admin/companies_deliveries/create", name="create_company_delivery")
     */
    public function createCompanyDelivery(Request $request)
    {
        $company = new CompanyDelivery();
        $form = $this->createForm(CompanyDeliveryType::class, $company);
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
    }

    /**
     * @Route("/admin/company_delivery/{id}/modify", name="modify_company_delivery")
     */
    public function modifyCompanyDelivery(Request $request, $id)
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');
        $option = [];
        if(!in_array("All", $company->getArea()))
            $option['all_france_value'] = "no";
        else 
            $option['all_france_value'] = "yes";
        $form = $this->createForm(CompanyDeliveryType::class, $company, $option);
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
                if(in_array("All", $company->getArea()))
                    $company->setArea([]);
                $this->getDoctrine()->getManager()->persist($company);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('choice_departement_company_delivery', ['id' => $company->getId()]);
            }
        }
        return $this->render('admin/commands/deliveries/companies_deliveries/form_company_delivery.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'create' => false, 'id' => $company->getId(), 
            'name' => $company->getName()]);
    }

    /**
     * @Route("/admin/company_delivery/{id}/choice_departement", name="choice_departement_company_delivery")
     */
    public function choiceDepartementCompanyDelivery(Request $request, $id)
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');
        $form = $this->createForm(ChoiceDepartmentType::class, $company);
        $form->handleRequest($request);
        $errors = array();
        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($company);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('company_delivery', ['id' => $id]);
        }
        return $this->render('admin/commands/deliveries/companies_deliveries/manage_departements.html.twig', 
            ['form' => $form->createView(), 'errors' => $errors, 'name' => $company->getName()]);
    }

    /**
     * @Route("/admin/company_delivery/{id}/activate", name="activate_deactivate_company_delivery")
     */
    public function activateDeactivateCompanyDelivery($id)
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');
        $company->setActivate(!$company->getActivate());
        //Attention lorsque l'activité d'une entreprise est modifiée ses types sont modifiés avec.
        foreach ($company->getType() as $type) {
            $type->setActivate($company->getType());
            $this->getDoctrine()->getManager()->persist($type);
        }
        $this->getDoctrine()->getManager()->persist($company);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('company_delivery', ['id' => $id]);
    }

    /**
     * @Route("/admin/company_delivery/{id}/delete", name="delete_company_delivery")
     */
    public function deleteCompanyDelivery($id)
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');
        $company->setDelete(true);
        //Attention la suppression d'une entreprise supprime ses types avec.
        foreach ($company->getType() as $type) {
            $type->setDelete(true);
            $this->getDoctrine()->getManager()->persist($type);
        }
        $this->getDoctrine()->getManager()->persist($company);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('companies_deliveries');
    }

    //
    //Partie TypeDelivery.
    //

    /**
     * @Route("/admin/types_deliveries", name="types_deliveries")
     */
    public function typesDeliveries()
    {
        $types = $this->getDoctrine()->getRepository(TypeDelivery::class)->findBy(['delete' => false]);

        return $this->render('admin/commands/deliveries/types_deliveries/types_deliveries.html.twig', ['types' => $types]);
    }

    /**
     * @Route("/admin/type_delivery/{id}", name="type_delivery")
     */
    public function typeDelivery($id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        
        return $this->render('admin/commands/deliveries/types_deliveries/type_delivery.html.twig', ['type' => $type]);
    }

    /**
     * @Route("/admin/types_deliveries/create", name="create_type_delivery")
     */
    public function createTypeDelivery(Request $request)
    {
        $type = new TypeDelivery();
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
            [ 'form' => $form->createView(), 'errors' => $errors, 'create' => true]);
    }

    /**
     * @Route("/admin/type_delivery/{id}/modify", name="modify_type_delivery")
     */
    public function modifyTypeDelivery(Request $request, $id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(!is_null($type))
            return $this->redirectToRoute('types_deliveries');
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
            [ 'form' => $form->createView(), 'errors' => $errors, 'create' => false, 'id' => $type->getId(), 
            'name' => $type->getName()]);
    }

    /**
     * @Route("/admin/type_delivery/{id}/activate", name="activate_deactivate_type_delivery")
     */
    public function activateDeactivateTypeDelivery($id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        $type->setActivate(!$type->getActivate());
        $this->getDoctrine()->getManager()->persist($type);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('type_delivery', ['id' => $id]);
    }

    /**
     * @Route("/admin/type_delivery/{id}/delete", name="delete_type_delivery")
     */
    public function deleteTypeDelivery($id)
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');
        $type->setDelete(true);
        $this->getDoctrine()->getManager()->persist($type);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('types_deliveries');
    }

    /**
     * @Route("admin/company_delivery/{id}/types_deliveries", name="types_by_company")
     */
    public function typesDeliveriesByCompany($id) 
    {
        $company = $this->getDoctrine()->getRepository(CompanyDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($company))
            return $this->redirectToRoute('companies_deliveries');

        return $this->render('admin/commands/deliveries/types_deliveries/types_deliveries_by_company_delivery.html.twig',
            ['company' => $company]);
    }

    //
    //Partie Delivery.
    //

    /**
     * @Route("admin/deliveries", name="deliveries")
     */
    public function deliveries() 
    {
        $deliveries = $this->getDoctrine()->getRepository(Delivery::class)->findBy(['delete' => false]);

        return $this->render('admin/commands/deliveries/deliveries/deliveries.html.twig', ['deliveries' => $deliveries]);
    }

    /**
     * @Route("admin/delivery/{id}", name="delivery")
     */
    public function delivery($id) 
    {
        $delivery = $this->getDoctrine()->getRepository(Delivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($delivery))
            return $this->redirectToRoute('deliveries');
        
        return $this->render('admin/commands/deliveries/deliveries/delivery.html.twig', ['delivery' => $delivery]);
    }

    /**
     * @Route("admin/type_delivery/{id}/deliveries", name="deliveries_by_type")
     */
    public function deliveriesByType($id) 
    {
        $type = $this->getDoctrine()->getRepository(TypeDelivery::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($type))
            return $this->redirectToRoute('types_deliveries');

        return $this->render('admin/commands/deliveries/deliveries/deliveries_by_type_delivery.html.twig', ['type' => $type]);
    }


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
        try {
            $this->filesSystem->remove($this->getParameter($parameter_directory).'/'.$nameImage);
            return false;
        } catch( IOExceptionInterface $e) {
            return $e;
        }
    }

}
