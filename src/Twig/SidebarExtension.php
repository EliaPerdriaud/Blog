<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SidebarExtension extends AbstractExtension {

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var Environment
     */
    private $twig;

    /**
     *@var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CommentRepository $commentRepository, Environment $twig, CategoryRepository $categoryRepository){
        $this->commentRepository = $commentRepository;
        $this->twig=$twig;
        $this->categoryRepository=$categoryRepository;
    }

    public function getFunctions(): array
    {
        return[
            new TwigFunction('sidebar', [$this, 'getSidebar'], ['is_safe'=>['html']])
        ];
    }

    public function getSidebar() : string
    {
        $comments = $this->commentRepository->findLatest();
        $categories= $this->categoryRepository->findCatWithPost();
        return $this->twig->render('user/post/sidebar.html.twig',[
            'comments'=>$comments,
            'categories'=>$categories
        ]);
    }
}