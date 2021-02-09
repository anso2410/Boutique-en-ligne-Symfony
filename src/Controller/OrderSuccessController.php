<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order= $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);


        if (!$order || $order->getUser() != $this->getUser() ) {
            return $this->redirectToRoute('home');
        }

        // Modifier le statut isPaid de notre commande en mettant 1 si payée.
       // $cart->remove(); test car paiement réel non actif.
        if ($order->getState() == 0 ) {
            // vider la session "cart".
            $cart->remove();
            //Modifier le statut isPaid
            $order->setState(1);
            $this->entityManager->flush();


            // Envoyer un email à notre client pour lui confirmer sa commande.
            $mail = new Mail();
            $content ="Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande.<br><br/>Lorem ipsum dolor sit amet, consectetexplicabo fugit, harum inventore molestias optio repellendus suscipit voluptas.Asperiores debitis distinctio, esse, est in inventore,temporibus voluptates. Ab corporis eveniet fugit nulla odio officiis quisquam repellendus saepe!";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(),'Votre commande La Boutique d\'Anso est bien validée', $content);

        }

        //Afficher les quelques informations de la commande du client.




        return $this->render('order_success/index.html.twig', [
            'order' => $order

        ]);
    }
}
