<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\BaseResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        $notification_error = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            // verifier que l'utilisateur n'existe pas déjà en BDD.
            $search_email =  $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = new Mail();
                $content ="Bonjour ".$user->getFirstname()."<br>Bienvenue sur la première boutique dédiée au made in Belgium.<br><br>Lorem ipsum dolor sit amet, consectetexplicabo fugit, harum inventore molestias optio repellendus suscipit voluptas.Asperiores debitis distinctio, esse, est in inventore,temporibus voluptates. Ab corporis eveniet fugit nulla odio officiis quisquam repellendus saepe!";
                $mail->send($user->getEmail(), $user->getFirstname(),"Bienvenue sur La Boutique d'Anso", $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte. ";
            }else {

                $notification_error = "L'email que vous avez renseigné existe déjà.";
            }





        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
            'notification_error' => $notification_error
        ]);
    }
}
