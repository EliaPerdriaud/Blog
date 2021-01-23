<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository as PostRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PostController extends AbstractController
{

    /**
     * @Route("/admin/post/list", name="admin.post.list")
     * @Route("/post", name="post")
     */
    public function list(HttpFoundationRequest $request, PaginatorInterface $paginator, PostRepository $pr): Response
    {
        $route = $request->attributes->get('_route');

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $EveryPost=$postRepository->findAll();
        $posts = $pr->findAllPublished();

        $donnees=$posts;

        $posts=$paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            5
        );

        if($route=="admin.post.list"){
            return $this->render('admin/post/list.html.twig', [
                'posts' => $EveryPost,
            ]);
        }
        else{
            return $this->render('user/post/list.html.twig', [
                'posts' => $posts,
            ]);
        }
    }

        /**
     * @Route("/admin/post/create", name="post.create")
     */
    public function create(HttpFoundationRequest $request): Response
    {
        $post = new Post();

        $form=$this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $date=new DateTime();
            $slugger = new AsciiSlugger();
            $post->setSlug($slugger->slug($post->getTitle()));
            $post->setCreatedAt($date);
            $post->setUpdatedAt($date);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $slug = $post->getSlug();
            return $this->redirectToRoute('admin.post.show', ['slug' => $slug]);
        }

        return $this->render('admin/post/create.html.twig', [
        'form' => $form->createView(),
        ]);
    }

        /**
     * @Route("/post/{slug}", name="post.show")
     * @Route("/admin/post/{slug}", name="admin.post.show")
     */
    public function show(String $slug, HttpFoundationRequest $request): Response
    {
        $route = $request->attributes->get('_route');

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->findOneBy(['slug' => $slug]);

        if($route=="admin.post.show"){
            return $this->render('admin/post/show.html.twig', [
                'post' => $post,
            ]);
        }

        else{
            return $this->render('user/post/show.html.twig', [
                'post' => $post,
            ]);
        }
    }

        /**
     * @Route("/admin/post/{id}/remove", name="admin.post.remove")
     * 
     */
    public function remove(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->find($id);

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('admin.post.list');
    }

        /**
     * @Route("/admin/post/{id}/edit", name="admin.post.edit")
     */
    public function edit(int $id,  HttpFoundationRequest $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->find($id);
        $date=new DateTime();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setUpdatedAt($date);
            $entityManager->persist($post);
            $entityManager->flush();
            $slug = $post->getSlug();
            return $this->redirectToRoute('admin.post.show', ['slug' => $slug]);
        }
        return $this->render('admin/post/edit.html.twig', [
            'form' => $form->createView(),
            ]);

    }
}
