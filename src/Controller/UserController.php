<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Command\Address;
use App\Entity\Command\Command;
use App\Entity\User\Access;
use App\Entity\User\User;
use App\Entity\User\Comment;

use App\Form\Type\User\UserType;
use App\Form\Type\User\ChangePasswordType;
use App\Form\Type\User\UploadImageType;
use App\Form\Type\User\ChangeMailType;
use App\Form\Type\Command\ChangeAddressType;

class UserController extends AbstractController
{

	private $passwordEncoder;

    private $filesSystem;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->filesSystem = new Filesystem();
    }

    /**
     * @Route("/store/user", name="store_user")
     */
    public function infoUser()
    {
        if($this->getUser()->getRoles()[0] != "ROLE_USER")
            return $this->redirectToRoute('store');

        return $this->render('store/user/info_user/info_user.html.twig', ['user' => $this->getUser(), 'basket' => $this->getBasket()]);
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
            return $this->render('store/user/create_user.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors]);
        } 
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("/store/user/change_password", name="store_change_password")
     */
    public function changePassword(Request $request) 
    {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
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
            return $this->render('store/user/info_user/change_password.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors, 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("/store/user/change_mail", name="store_change_mail")
     */
    public function changeMailAddress(Request $request)
    {
        if($this->getUser()->getRoles()[0] === "ROLE_USER"){
            $form = $this->createForm(ChangeMailType::class);
            $form->handleRequest($request);
            $errors = array();
            if($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                if(!$this->passwordEncoder->isPasswordValid($this->getUser(), $form->get('password')->getData())) {
                    $errors[] = "Le mot de passe est incorrecte.";
                    $valid = false;
                }
                if($valid) {
                    $this->getUser()->setValid(false);
                    $this->getUser()->setEmail($form->get('email')->getData());
                    $this->getDoctrine()->getManager()->persist($this->getUser());
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('store_user');
                }
            }
            return $this->render('store/user/info_user/change_mail.html.twig', 
                ['form' => $form->createView(), 'errors' => $errors, 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute('store');
    }

    //--- Méthode à vérifier avec un serveur fonctionnel et  configurer. ---
    /**
     * @Route("/store/email/verify_account", name="store_send_email_verify")
     */
    public function sendEmailVerify(/*MailerInterface $mailer*/)
    {
        if(!$this->getUser()->getValid()) {
            var_dump(uniqid());
            die();
            $access = new Access();
            $access->setCode(uniqid());
            $email = (new Email())->from('')->to($this->getUser()->getEmail())->subject("Vérification d'adresse mail.")
                ->htmlTemplate('store/email/email_verify_email_address.html.twig')
                ->context(['username' => $this->getUser()->getUsername(), 'code' => $access->getCode()]);
            try {
                $mailer->send($email);
            } catch(TransportExceptionInterface $e) {
                return $this->redirectToRoute('store_user');
            }
            $this->getUser()->addAccess($access);
            $this->getDoctrine()->getManager()->persit($access);
            $this->getDoctrine()->getManager()->persit($this->getUser());
            $this->getDoctrine()->getManager()->flush();
            return $this->render('store/email/send_mail.html.twig', 
                ['email_address' => $$this->getUser()->getEmail(), 'username' => $this->getUser()->getUsername()]);    
        }
        return $this->redirectToRoute('store_user');
    }

    //--- Méthode à vérifier après la méthode sendEmailVerify. ---
    /**
     * @Route("store/user/verify/{code}", name="store_user_verify")
     */
    public function verifyUser($code)
    {
        $access = $this->getDoctrine()->getRepository(Access::class)
            ->findOneBy(['code' => $code, 'user' => $this->getUser()->getId(), 'used' => false]);
        if(!is_null($access)) {
            $access->setUsed(true);
            $this->getUser()->setValid(true);
            $this->getDoctrine()->getManager()->persist($access);
            $this->getDoctrine()->getManager()->persist($this->getUser());
            $this->getDoctrine()->getManager()->flush();
            $this->render('store/store/user/info_user/message_verified_email_address.html.twig', 
                ['username' => $this->getUser()->getUsername(), 'email_address' => $this->getUser()->getEmail()]);
        }
        return $this->redirectToRoute('store_user');
    }

    /**
     * @Route("store/user/delete", name="store_user_delete")
     */
    public function deleteUser() 
    {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            $user->setDelete(true);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_logout');
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/change_image", name="store_user_change_image")
     */
    public function changeImageUser(Request $request) 
    {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $form = $this->createForm(UploadImageType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image')->getData();
                if($image) {
                    $originalImagename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeImageName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', 
                        $originalImagename);
                    $newImagename = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
                    
                    try {
                        $image->move($this->getParameter('user_image_directory'), $newImagename);
                    } catch(FileException $e) {
                        return $this->render('store/user/change_image.html.twig', 
                            ['form' => $form->createView(), 'errors' => $e]);
                    }
                    if($this->getUser()->getImgFileName() !== null) {
                        try {
                            //$this->filesSystem->remove(['file', $this->getParameter('user_image_directory').'/', $this->getUser()->getImgFileName()]);
                            $this->filesSystem->remove($this->getParameter('user_image_directory').'/'.$this->getUser()
                                ->getImgFileName());
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
            return $this->render('store/user/info_user/change_image.html.twig', ['form' => $form->createView(), 'basket' => $this->getBasket()]);    
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/change_address", name="store_user_change_address")
     */
    public function changeAddressUser(Request $request) {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $address = new Address();
            $form = $this->createForm(ChangeAddressType::class, $address);
            $errors = array();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                if(strlen($form->getData()->get('zipCode')) != 5) {
                    if(strlen($form->getData()->get('zipCode')) === 4) 
                        $address->setZipCode("0".$address->getZipCode());
                    else
                        return $this->render('store/user/change_address.html.twig', 
                            ['form' => $form->createView(), 'errors' => $errors]);
                }
                $former_address = $this->getUser()->getLive();
                if($former_address !== null) {
                    $former_address->removeBelong($this->getUser());
                    if($former_address->isEmptyCommand() && $former_address->isEmptyBelong())
                        $former_address->setDelete(true);
                }
                $address->addBelong($this->getUser());
                $this->getDoctrine()->getManager()->persist($this->getUser());
                $this->getDoctrine()->getManager()->persist($address);
                if($former_address !== null)
                    $this->getDoctrine()->getManager()->persist($former_address);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('store_user');
            }
            return $this->render('store/user/info_user/change_address.html.twig', 
                ['form' => $form->createView(), 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/commands", name="store_user_commands")
     */
    public function showCommandsUser() {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $commands = $this->getDoctrine()->getRepository(Command::class)
                ->findBy(['user' => $this->getUser(), 'delete' => false, 'isBasket' => false]);

            return $this->render('store/user/command/commands.html.twig', ['commands' => $commands, 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/command/{id}", name="store_user_command")
     */
    public function showCommandUser($id) {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $command = $this->getDoctrine()->getRepository(Command::class)->findOneBy(['user' => $this->getUser(), 'id' => $id]);

            if(is_null($command)) 
                return $this->redirectToRoute('store_user_commands');

            if($command->getIsBasket() || !$command->getCompleted())
                return $this->redirectToRoute('store_user_commands');

            return $this->render('store/user/command/command.html.twig', ['command' => $command, 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute('store');
    }

    /**
     * @Route("store/user/comments", name="store_user_comments")
     */
    public function showCommentsUser() {
        if($this->getUser()->getRoles()[0] === "ROLE_USER") {
            $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['delete' => false, 'user' => $this->getUser()]);

            return $this->render('store/user/comments.html.twig', ['comments' => $comments, 'basket' => $this->getBasket()]);
        }
        return $this->redirectToRoute("store");
    }

    protected function getBasket()
    {
        return ($this->getUser() !== null)?($this->getUser()->getBasket()):null;
    }

    /*protected function activateVerifyUser($id) 
    {
        $success = false;
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if(!is_null($user)) {
            $user->setValid(true);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $success = true;
        }
        return $success;
    }*/

}

