<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/comment/create/{idPost}", name="comment.create")
     */
    public function create(int $idPost, Request $request):Response
    {
        $comment = new Comment();

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->find($idPost);

        $form=$this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date  = new DateTime();
            $comment = $form->getData();
            $comment->setValid(false);
            $comment->setCreatedAt($date);
            $comment->setPost($post);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $id = $comment->getId();
            return $this->redirectToRoute('comment.show', ['id' => $id]);
        }

        return $this->render('user/comment/create.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/comment/list", name="admin.comment.list")
     */
    public function list(Request $request): Response
    {
        $route = $request->attributes->get('_route');

        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findAll();

        if($route=="admin.comment.list"){
            return $this->render('admin/comment/list.html.twig', [
                'comments' => $comments,
            ]);
        }

    /*    else{
            return $this->render('user/comment/list.html.twig', [
                'comments' => $comments,
            ]);
        } */
    }

    
    /**
     * @Route("/comment/{id}", name="comment.show")
     * @Route("/admin/comment/{id}", name="admin.comment.show")
     */
    public function show(int $id, Request $request): Response
    {
        $route = $request->attributes->get('_route');

        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comment = $commentRepository->find($id);
        $post=$comment->getPost();


        if($route=="admin.comment.show"){
            return $this->render('admin/comment/show.html.twig', [
                'comment' => $comment,
                'post' => $post,
            ]);
        }

        else{
            return $this->render('user/comment/show.html.twig', [
                'comment' => $comment,
                'post' => $post,
            ]);
        }
    }

            /**
     * @Route("/admin/comment/{id}/remove", name="admin.comment.remove")
     * 
     */
    public function remove(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $post = $commentRepository->find($id);

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('admin.comment.list');
    }

    /**
     * @Route("/admin/comment/{id}/validate", name="admin.comment.validate")
     */
    public function validate(int $id, Request $request):Response{

        $entityManager = $this->getDoctrine()->getManager();
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comment = $commentRepository->find($id);
        $comment->setValid(true);
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('admin.comment.list');


    }

}
