<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



use App\Entity\User\User;
use App\Form\Type\User\UserType;
use App\Form\Type\User\ChangePasswordType;
use App\Form\Type\User\UploadImageType;


class StoreController extends AbstractController
{

    private $passwordEncoder;

    private $filesSystem;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->filesSystem = new Filesystem();
    }

    /**
     * @Route("/store", name="store")
     */
    public function index()
    {
        return $this->render('store/index.html.twig', [
            'controller_name' => 'StoreController/store',
        ]);
    }

    /**
     * @Route("/store/test", name="store_test")
     */
    public function test()
    {
        //$this->container->get('security.password_encoder');
        return $this->render('store/index.html.twig', [
            'controller_name' => 'StoreController/test',
        ]);
    }

    /**
     * @Route("/store/product", name="store_product")
     */
    public function showArticles(Request $request)
    {
        $var_products = $this->getDoctrine()->getRepository(VariantProduct::class)->findAll();
        
        return $this->render('store/index.html.twig', ['products' => $var_products]);
    }

    /**
     * @Route("/store/user", name="store_user")
     */
    public function infoUser()
    {
        if($this->getUser() !== null)
            return $this->render('store/user/info_user.html.twig', ['user' => $this->getUser()]);
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("/store/create_user", name="store_create_user")
     */
    public function createUser(Request $request) 
    {
        if(is_null($this->getUser())) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                $exist = $this->getDoctrine()->getRepository(User::class)->findBy(['username' => $user->getUsername()]);
                if(!empty($exist)) {
                    $errors[] = "Le nom d'utilisateur existe déjà.";
                    $valid = false;
                } 
                if($user->getPassword() != $form->get('verifyPassword')->getData()) {
                    $errors[] = "La vérification du mot de passe et le mot de passe sont différents.";
                    $valid = false;
                }
                if($valid) {
                    $user->setRoles(['ROLE_USER']);
                    $user->setAdmin(false);
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute("app_login");
                }
            }
            return $this->render('store/user/create_user.html.twig', ['form' => $form->createView(), 'errors' => $errors]);
        } 
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("/store/user/change_password", name="store_change_password")
     */
    public function changePassword(Request $request) 
    {
        if($this->getUser() !== null) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            $form = $this->createForm(ChangePasswordType::class/*, $user*/);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                //if(!$this->container->get('security.password_encoder')->isPasswordValid($this->getUser(), $form->get('formerPassword')->getData())) {
                if(!$this->passwordEncoder->isPasswordValid($this->getUser(), $form->get('formerPassword')->getData())) {
                    $errors[] = "Le mot de passe est incorrecte.";
                    $valid = false;
                }
                if($form->get('password')->getData() != $form->get('verifyPassword')->getData()) {
                    $errors[] = "La vérification du nouveau mot de passe et le nouveau mot de passe sont différents.";
                    $valid = false;
                }
                if($valid) {
                    $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('password')->getData()));
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute("store_user");
                }
            }
            return $this->render('store/user/change_password.html.twig', ['form' => $form->createView(), 'errors' => $errors]);
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/delete", name="store_user_delete")
     */
    public function deleteUser() 
    {
        if($this->getUser() !== null) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            $user->setDelete(true);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_logout');
        }
        return $this->redirectToRoute('store_user');
    }

    /**
     * @Route("store/user/verify", name="store_user_verify")
     */
    public function verifyUser()
    {
        //Envoyer un mail de vérification d'utilisateur.


        return $this->redirectToRoute('store_user');
    }


    public function activateVerifyUser($id) 
    {
        $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setValid(true);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('store_test');
    }

    /**
     * @Route("store/user/change_image", name="store_user_change_image")
     */
    public function changeImageUser(Request $request) 
    {
        if($this->getUser() !== null) {
            $form = $this->createForm(UploadImageType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();
                if($image) {

                    $originalImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeImageName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
                    $newImagename = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
                    
                    try {
                        $image->move($this->getParameter('user_image_directory'), $newImagename);
                    } catch(FileException $e) {
                        return $this->render('store/user/change_image.html.twig', ['form' => $form->createView(), 'errors' => $e]);
                    }

                    if($this->getUser()->getImgFileName() !== null) {
                        try {
                            //$this->filesSystem->remove(['file', $this->getParameter('user_image_directory').'/', $this->getUser()->getImgFileName()]);
                            $this->filesSystem->remove($this->getParameter('user_image_directory').'/'.$this->getUser()->getImgFileName());
                        } catch( IOExceptionInterface $e) {
                            return $this->redirectToRoute('store_user', ['errors' => $e]);
                        }
                    }

                    $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
                    $user->setImgFileName($newImagename);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();

                }
                return $this->redirectToRoute('store_user');
            }
            return $this->render('store/user/change_image.html.twig', ['form' => $form->createView()]);    
        }
        return $this->redirectToRoute('login');
    }

}
