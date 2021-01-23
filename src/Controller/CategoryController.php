<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;


class CategoryController extends AbstractController
{

    /**
     * @Route("/admin/category/list", name="admin.category.list")
     */
    public function list(HttpFoundationRequest $request): Response
    {
        $route = $request->attributes->get('_route');

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        if($route=="admin.category.list"){
            return $this->render('admin/category/list.html.twig', [
                'categories' => $categories,
            ]);
        }

    /*    else{
            return $this->render('user/category/list.html.twig', [
                'categories' => $categories,
            ]);
        } */
    }


    /**
     * @Route("/admin/category/create", name="category.create")
     */
    public function create(HttpFoundationRequest $request): Response
    {
        $category = new Category();

        $form=$this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            $id = $category->getId();
            return $this->redirectToRoute('admin.category.show', ['id' => $id]);
        }

        return $this->render('admin/category/create.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}/show", name="category.show")
     * @Route("/admin/category/{id}/show", name="admin.category.show")
     */
    public function show(int $id, HttpFoundationRequest $request): Response
    {
        $route = $request->attributes->get('_route');

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoryRepository->find($id);
        $post=$category->getPosts();

        if($route=="admin.category.show"){
            return $this->render('admin/category/show.html.twig', [
                'category' => $category,
                'post' => $post
            ]);
        }

        else{
            return $this->render('user/category/show.html.twig', [
                'category' => $category,
                'post' => $post
            ]);
        }
    }

    /**
     * @Route("/admin/category/{id}/remove", name="admin.category.remove")
     * 
     */
    public function remove(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('admin.category.list');
    }

    /**
     * @Route("/admin/category/{id}/edit", name="admin.category.edit")
     */
    public function edit(int $id,  HttpFoundationRequest $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();
            $id = $category->getId();
            return $this->redirectToRoute('admin.category.show', ['id' => $id]);
        }
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            ]);

    }

    
}
