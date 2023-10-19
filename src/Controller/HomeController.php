<?php

namespace App\Controller;

use App\Entity\Post;
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
                'form' => $form
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
