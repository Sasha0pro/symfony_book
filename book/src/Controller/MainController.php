<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ManagerRegistry $managerRegistry): Response
    {
    $author = $this->getUser();
    $book = $managerRegistry->getRepository(Book::class)->findAll();
    if (!$author){
        return  $this->redirectToRoute('app_login');
    }

        return $this->render('main/index.html.twig', [
            'book' => $book,
        ]);
    }
}
