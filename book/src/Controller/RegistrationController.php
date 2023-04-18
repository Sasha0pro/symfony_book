<?php

namespace App\Controller;

use App\Form\AuthorType;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

class RegistrationController extends AbstractController
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request,ManagerRegistry $managerRegistry): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class,$author)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $author->setPassword($this->passwordHasher->hashPassword($author,$author->getPassword()));
            $author->setRoles(['ROLE_USER']);
            $em = $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
