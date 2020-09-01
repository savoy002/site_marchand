<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User\User;
use App\Entity\User\Comment;
use App\Form\Type\User\UserType;


class AdminController extends AbstractController
{

    //Le nombre d'utilisateurs affichés par page.
    const NUMBER_BY_PAGE = 5;

    //Le nombre de caractères par commentaire affiché dans la gestion des commentaires.
    const NUMBER_CHARACTERS = 30;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }


    //Partie User.

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/test", name="test_admin")
     */
    public function test() 
    {
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("/admin/users", name="users")
     */
    public function users(Request $request)
    {
        $page = $request->request->get('page');
        $former_request = array();
        $errors = array();
        $criteria = ['number_by_page' => self::NUMBER_BY_PAGE];

        if($request->request->get('research') === "research") {
            if($request->request->get('username') != "" && $request->request->get('username') !== null) {
                $criteria['username'] = array('value' => $request->request->get('username'), 
                    'type' => $request->request->get('type_research_username'));
                $former_request['username'] = $request->request->get('username');
                $former_request['type_research_username'] = $request->request->get('type_research_username');
            }
            if($request->request->get('email') != "" && $request->request->get('email') !== null) {
                $criteria['email'] = array('value' => $request->request->get('email'),
                    'type' => $request->request->get('type_research_email'));
                $former_request['email'] = $request->request->get('email');
                $former_request['type_research_email'] = $request->request->get('type_research_email');
            }
            if($request->request->get('roles') != "" && $request->request->get('roles') !== null) {
                $criteria['roles'] = $request->request->get('roles');
                $former_request['roles'] = $request->request->get('roles');
            }
            if($request->request->get('valid') != "" && $request->request->get('valid') !== null) {
                $criteria['valid'] = $request->request->get('valid');
                $former_request['valid'] =  $request->request->get('valid');
            }
            if($request->request->get('createdBy') != "" && $request->request->get('createdBy') !== null) {
                $criteria['createdBy'] = $request->request->get('createdBy');
                $former_request['createdBy'] =  $request->request->get('createdBy');
            }

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

            if($request->request->get('orderBy_sortBy') != "" && $request->request->get('orderBy_sortBy') !== null) {
                $criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }

            $number_users = $this->getDoctrine()->getRepository(User::class)->adminResearchNumberUsers($criteria)[0][1];
            $number_pages = 
                intval( $number_users / self::NUMBER_BY_PAGE ) + ( ( $number_users % self::NUMBER_BY_PAGE === 0 )?0:1 );

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

            $users = $this->getDoctrine()->getRepository(User::class)->adminResearchUser($criteria);

        } else {
            $criteria['page'] = 0;
            $page = 1;
            $users = $this->getDoctrine()->getRepository(User::class)->adminResearchUser($criteria);
            $number_users = intval($this->getDoctrine()->getRepository(User::class)->countNumberUsers()[0][1]);
            $number_pages = 
                intval( $number_users / self::NUMBER_BY_PAGE ) + ( ( $number_users % self::NUMBER_BY_PAGE === 0 )?0:1 );
        }

    	return $this->render('admin/users/users/users.html.twig', ['users' => $users, 'number_pages' => $number_pages, 
            'page' => $page, 'request' => $former_request, 'errors' => $errors]);
    }

    /**
     * @Route("/admin/user/{id}/", name="user")
     */
    public function showUser($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy( ['id' => $id, 'delete' => false] );
        return $this->render('admin/users/users/user.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/admin/users/create_admin_user/", name="admin_create_user")
     */
    public function createAdminUser(Request $request) 
    {
        if($this->getUser()->getSuperAdmin()) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                //Regarde si le nom d'utilisateur n'a pas déjà été choisie.
                $exist = $this->getDoctrine()->getRepository(User::class)->findBy(['username' => $user->getUsername()]);
                if(!empty($exist)) {
                    $errors[] = "Le nom d'utilisateur existe déjà.";
                    $valid = false;
                }
                //Regarde si les deux mot de passes sont identiques.
                if($user->getPassword() != $form->get('verifyPassword')->getData()) {
                    $errors[] = "La vérification du mot de passe et le mot de passe sont différents.";
                    $valid = false;
                }
                if($valid) {
                    $user->setAdmin(true);
                    $user->setValid(true);
                    //Cryte le mot de passe.
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute("users");
                }
            }
            return $this->render('admin/users/users/create_user.html.twig', ['form' => $form->createView(), 'errors' => $errors]);
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/users/{id}/valid", name="valid_user")
     */
    public function userValid($id)
    {
        if($this->getUser()->getSuperAdmin()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $user->setValid(!$user->getValid());
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }
    	return $this->redirectToRoute('user', ['id' => $id]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="delete_user")
     */
    public function userDelete($id) 
    {
        if($this->getUser()->getSuperAdmin()) {
        	$user = $this->getDoctrine()->getRepository(User::class)->find($id);
        	if(!$user->getSuperAdmin()) {
        		$user->setDelete(true);
                $comments = $this->getDoctrine()->getRepository(Comment::class)->findCommentsByUser($user);
                foreach($comments as $comment) {
                    $comment->setDelete(true);
                    $this->getDoctrine()->getManager()->persist($comment);
                }
                $this->getDoctrine()->getManager()->persist($user);
    	    	$this->getDoctrine()->getManager()->flush();
        	}
        }
    	return $this->redirectToRoute('users');
    }


    //Partie Comment.

    /**
     * @Route("admin/comments", name="comments")
     */
    public function comments(Request $request)
    {
        $errors = array();
        $former_request = array();
        //Place le nombre de commentaire par page dans les paramètre de la recherche.
        $criteria = ['number_by_page' => self::NUMBER_BY_PAGE];
        //Recherche la demande de page de l'administrateur si elle existe.
        $page = $request->request->get('page');

        //Gestion de la sélection si il y an a une.
        if($request->request->get('research') === "research") {
            //Création des différents paramètres de la recherche.
            if($request->request->get('text') != "" && $request->request->get('text') !== null) {
                $criteria['text'] = $request->request->get('text');
                $former_request['text'] = $request->request->get('text');
            }
            if($request->request->get('mark') != "" && $request->request->get('mark') !== null) {
                if($request->request->get('mark') >= 1 &&  $request->request->get('mark') <= 5) {
                    $criteria['mark'] = array('value' =>  $request->request->get('mark'), 
                        'type' => $request->request->get('type_research_mark') );
                    $former_request['mark'] = $request->request->get('mark');
                    $former_request['type_research_mark'] = $request->request->get('type_research_mark');
                }
            }
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
            //Ajout des ordres de recherches.
            if($request->request->get('orderBy_sortBy') != "none" && $request->request->get('orderBy_sortBy') !== null) {
                $criteria['orderBy'] = 
                    array('attribut' => $request->request->get('orderBy_sortBy'), 'order' =>  $request->request->get('orderBy_sortDir'));
                $former_request['orderBy_sortBy'] = $request->request->get('orderBy_sortBy');
                $former_request['orderBy_sortDir'] = $request->request->get('orderBy_sortDir');
            }
            //Calcul le nombre de commentaires et de pages.
            $number_comments = $this->getDoctrine()->getRepository(Comment::class)
                ->adminResearchNumberComments($criteria)[0][1];
            $number_pages = 
                intval( $number_comments / self::NUMBER_BY_PAGE ) + 
                    ( ( $number_comments % self::NUMBER_BY_PAGE === 0 )?0:1 );
            //Ajout du numéro de page.
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
            //Ajoute le numéro de page.
            $page = 1;
            //Calcul le nombre de commentaires et de pages.
            $number_comments = intval($this->getDoctrine()->getRepository(Comment::class)
                ->countNumberComments()[0][1]);
            $number_pages = 
                intval( $number_comments / self::NUMBER_BY_PAGE ) + 
                    ( ( $number_comments % self::NUMBER_BY_PAGE === 0 )?0:1 );
        }
        
        //Recherche les commentaires à retourner.
        $comments = $this->getDoctrine()->getRepository(Comment::class)->adminResearchComment($criteria);

        return $this->render("admin/users/comments/comments.html.twig", 
            ['comments' => $comments, 'errors' => $errors, 'page' => $page, 'number_pages' => $number_pages, 
             'request' => $former_request, 'number_characters' => self::NUMBER_CHARACTERS]);
    }
    
    /**
     * @Route("admin/comment/{id}", name="comment")
     */
    public function comment($id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($comment))
            return $this->redirectToRoute('comments');

        return $this->render("admin/users/comments/comment.html.twig", ['comment' => $comment]);
    }

    /**
     * @Route("admin/comment/{id}/delete", name="delete_comment")
     */
    public function commentDelete($id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($comment))
            return $this->redirectToRoute('comments');

        $comment->setDelete(true);
        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('comment');
    }

    /**
     * @Route("admin/user/{id}/comments", name="comments_by_user")
     */
    public function commentsByUser($id) 
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id, 'delete' => false]);
        if(is_null($user))
            return $this->redirectToRoute('users');

        return $this->render('admin/users/comments/comments_by_user.html.twig', 
            ['user' => $user, 'number_characters' => self::NUMBER_CHARACTERS]);
    }

}
