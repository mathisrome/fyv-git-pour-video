<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\Manga;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/categories', name: 'categories_')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->json(
            $categories,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "category:list"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function one(Category $category): Response
    {
        return $this->json(
            $category,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "category:read"
                ]
            ]
        );
    }

    #[Route('', name: 'new', methods: ['POST'])]
    public function new(
        #[MapRequestPayload] Category $category,
        EntityManagerInterface        $em,
        ValidatorInterface            $validator
    ): Response
    {
        $violations = $validator->validate($category);

        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($category);
        $em->flush();

        return $this->json(
            $category,
            Response::HTTP_CREATED,
            context: [
                "groups" => [
                    "category:read"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(
        #[MapRequestPayload] Category $categoryRequest,
        Category                      $category,
        EntityManagerInterface        $em,
        ValidatorInterface            $validator
    ): Response
    {
        $violations = $validator->validate($categoryRequest);

        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $category->setName($categoryRequest->getName());

        $em->flush();

        return $this->json(
            $category,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "category:read"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Category               $category,
        EntityManagerInterface $em,
    ): Response
    {
        $em->remove($category);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    #[Route('/{categoryId}/mangas/{mangaId}', name: 'add_manga', methods: ['PUT'])]
    public function addManga(
        #[MapEntity(id: "categoryId")] Category $category,
        #[MapEntity(id: "mangaId")] Manga       $manga,
        EntityManagerInterface                  $em,
    ): Response
    {
        $category->addManga($manga);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    #[Route('/{categoryId}/mangas/{mangaId}', name: 'delete_manga', methods: ['DELETE'])]
    public function deleteManga(
        #[MapEntity(id: "categoryId")] Category $category,
        #[MapEntity(id: "mangaId")] Manga       $manga,
        EntityManagerInterface                  $em,
    ): Response
    {
        $category->removeManga($manga);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
