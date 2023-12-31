<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        $allPosts = $em->getRepository(Post::class)->findAll();

        if ($user) {
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $post->setUser($user);
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('app_home');
            }
            return $this->render('home/index.html.twig', [
                'form' => $form,
                'allPosts' => $allPosts
            ]);
        }
        return $this->render('home/index.html.twig', [
            'allPosts' => $allPosts
        ]);
    }

    #[Route('/post/{id}', name: 'app_post')]
    public function app_post(Int $id, EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $post = $em->getRepository(Post::class)->find($id);
        $allComments = $em->getRepository(Comment::class)->findBy(['post' => $post]);
        //->find() pour récuperer une seule données grace à une condition ex: ->find(['id' => 4]);
        //->findBy() pour récuperer plusieurs données grace à une condition ex: ->findBy(['nom' => 'Fontaine']);


        if ($user) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setUser($user);
                $comment->setPost($post);
                $em->persist($comment); //ajouter dans la db
                //->remove() enlever de la db
                $em->flush(); //faire la requete

                return $this->redirectToRoute('app_post', ['id' => $id]);
            }
            return $this->render('home/post.html.twig', [
                'form' => $form,
                'post' => $post,
                'allComments' => $allComments
            ]);
        }
        return $this->render('home/index.html.twig', [
            'post' => $post,
            'allComments' => $allComments
        ]);
    }
}
