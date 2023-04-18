<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BookController extends AbstractController
{
    #[Route('/book', name: 'create_book', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        /** @var Author $user */
        $user = $this->getUser();
        $book = new Book();
        $form = $this->createForm(BookType::class, $book)->handlerequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->persist($book->addAuthor($user));
            $em->flush();

            return $this->redirectToRoute('app_main');
        }
        return $this->render('book/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/books', name:'get_book_list', methods: ['GET'])]
    public function list(): Response
    {
        /** @var Author $user */
        $user = $this->getUser();

        return $this->render('book/mybook.html.twig', [
           'mybook' => $user->getBooks()->toArray(),
        ]);
    }

    #[Route('/book/{book_id}/content', name:'get_book_content')]
    #[ParamConverter('book', Book::class, options: ["id" => "book_id"])]
    public function getContent(Book $book): Response
    {
        return $this->render('book/content.html.twig',['content' => $book->getContent()]);
    }

    #[Route('/book/{book_id}', name:'update_book', methods: ['PUT'])]
    #[ParamConverter('book', Book::class, options: ["id" => "book_id"])]
    public function update(Book $book, ManagerRegistry $managerRegistry, Request $request): Response
    {
        $isEdit = $request->query->get('edit');
        $content = $request->request->get('content');
        if ($isEdit) { // Почему без использования формы?
            $book->setContent($content);
            $em = $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_main');
        }

        $bookContent = $book->getContent();
        return $this->render('book/edit.html.twig',['content' => $bookContent, 'id' => $book->getId()]);
    }

    #[Route('/book/{book_id}', name:'delete_book', methods: ['DELETE'])]
    #[ParamConverter('book', Book::class, options: ["id" => "book_id"])]
    public function delete(Book $book, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('app_main');
    }

}
