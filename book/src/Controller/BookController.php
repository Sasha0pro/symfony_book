<?php

namespace App\Controller;

use App\Entity\AuthorBook;
use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
class BookController extends AbstractController
{
    #[Route('/newbook', name: 'app_book')]
    public function index(Request $request,ManagerRegistry $managerRegistry): Response
    {
        $authorid = $this->getUser()->getId();
        $AuthorBook = new AuthorBook();
        $book = new Book();
        $form = $this->createForm(BookType::class,$book)->handlerequest($request);
        if ($form->isSubmitted() && $form->isValid()){
        $em = $managerRegistry->getManager();
        $em->persist($book);
        $em->flush();
        $AuthorBook->setAuthorId($authorid);
        $AuthorBook->setBookId($book->getId());
        $em->persist($AuthorBook);
        $em->flush();
        return $this->redirectToRoute('app_main');
        }
        return $this->render('book/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/mybook', name:'app_mybook')]
    public function MyBookAction(ManagerRegistry $managerRegistry){
        $authorId = $this->getUser()->getId();
        $AuthorBooks = $managerRegistry->getRepository(AuthorBook::class)->findBy([ 'AuthorId' => $authorId]);
       $myBooks = [];
        for ($i=0;$i<count($AuthorBooks);$i++){
            $bookId = $AuthorBooks[$i]->getBookId();
            $book = $managerRegistry->getRepository(Book::class)->findOneBy(['id' => $bookId]);
            $myBooks[] = $book;
        }

        return $this->render('book/mybook.html.twig',[
           'mybook' => $myBooks
        ]);
    }
    #[Route('book/content', name:'app_content')]
    public function ContentAction(ManagerRegistry $managerRegistry,Request $request){
        $id = $request->query->get('id');
        $book = $managerRegistry->getRepository(Book::class)->findOneBy(['id' => $id]);
        $bookContent = $book->getContent();
        return $this->render('book/content.html.twig',['content' => $bookContent]);
    }
    #[Route('book/edit', name:'app_edit')]
    public function EditAction(ManagerRegistry $managerRegistry,Request $request){
        $id = $request->query->get('id');
        $book = $managerRegistry->getRepository(Book::class)->findOneBy(['id' => $id]);
        $edit = $request->query->get('edit');
        $content = $request->request->get('content');
        if ($edit == true){
           $book->setContent($content);
           $em = $managerRegistry->getManager();
           $em->persist($book);
           $em->flush();
        return $this->redirectToRoute('app_main');
        }

        $bookContent = $book->getContent();
        return $this->render('book/edit.html.twig',['content' => $bookContent,'id' => $id]);
    }
    #[Route('book/delete', name:'app_content')]
    public function DeleteAction(ManagerRegistry $managerRegistry,Request $request){
        $id = $request->query->get('id');
        $book = $managerRegistry->getRepository(Book::class)->findOneBy(['id' => $id]);
        $em = $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('app_main');
    }

}
