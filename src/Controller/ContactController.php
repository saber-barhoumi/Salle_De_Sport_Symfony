<?php
// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function produitFront(): Response
    {
        return $this->render('produit/produitFront.html.twig');
    }

    #[Route('/send_email', name: 'send_email', methods: ['POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer)
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        $emailMessage = (new Email())
            ->from($email)
            ->to('your-email@example.com')  // Email destination
            ->subject($subject)
            ->text($message)
            ->html('<p>' . $message . '</p>');

        $mailer->send($emailMessage);

        $this->addFlash('success', 'Votre message a été envoyé avec succès!');
        return $this->redirectToRoute('produit_front');
    }
}
