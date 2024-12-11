<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ResetPasswordType;  // Assurez-vous d'avoir ce formulaire
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface; // Ajoutez cette ligne

class PasswordResetController extends AbstractController
{
    #[Route('/password-reset', name: 'app_password_reset')]
    public function requestReset(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un jeton unique
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $entityManager->flush();

                // Créer le lien de réinitialisation
                $resetUrl = $this->generateUrl('app_password_reset_token', [
                    'token' => $token, // Le token est passé dans l'URL
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                // Envoyer l'email avec le lien de réinitialisation
                $resetEmail = (new TemplatedEmail())
                    ->from(new Address('no-reply@example.com', 'No Reply'))
                    ->to($user->getEmail())
                    ->subject('Password Reset Request')
                    ->htmlTemplate('emails/password_reset.html.twig')
                    ->context([
                        'resetUrl' => $resetUrl, // Passer l'URL du reset dans le contexte de l'email
                    ]);

                $mailer->send($resetEmail);
                $this->addFlash('success', 'An email has been sent with instructions to reset your password.');
            } else {
                $this->addFlash('danger', 'No user found with this email address.');
            }
        }

        return $this->render('password_reset/request.html.twig');
    }

    #[Route('/reset/{token}', name: 'app_password_reset_token')]
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Invalid or expired reset token.');
            return $this->redirectToRoute('app_password_reset');
        }

        // Créer et traiter le formulaire de réinitialisation du mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setMotDePasse($hashedPassword);
            $user->setResetToken(null); // Supprimer le jeton après utilisation
            $entityManager->flush();

            $this->addFlash('success', 'Password successfully reset. You can now log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/reset.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token
        ]);
    }
}
