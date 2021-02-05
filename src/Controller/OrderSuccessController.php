<?php

namespace App\Controller;

use App\Classe\Cart;
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
        if (!$order->getIsPaid()) {
            // vider la session "cart".
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();


            // Envoyer un email à notre client pour lui confirmer sa commande.

        }

        //Afficher les quelques informations de la commande du client.




        return $this->render('order_success/index.html.twig', [
            'order' => $order

        ]);
    }
}
