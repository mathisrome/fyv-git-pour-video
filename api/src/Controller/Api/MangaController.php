<?php

namespace App\Controller\Api;

use App\Entity\Manga;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/mangas', name: 'mangas_')]
class MangaController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(MangaRepository $mangaRepository): Response
    {
        $mangas = $mangaRepository->findAll();

        return $this->json(
            $mangas,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "manga:list"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function one(Manga $manga): Response
    {
        return $this->json(
            $manga,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "manga:read"
                ]
            ]
        );
    }

    #[Route('', name: 'new', methods: ['POST'])]
    public function new(
        #[MapRequestPayload] Manga $manga,
        EntityManagerInterface     $em,
        ValidatorInterface         $validator
    ): Response
    {
        $violations = $validator->validate($manga);

        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($manga);
        $em->flush();

        return $this->json(
            $manga,
            Response::HTTP_CREATED,
            context: [
                "groups" => [
                    "manga:read"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(
        #[MapRequestPayload] Manga $mangaRequest,
        Manga                      $manga,
        EntityManagerInterface        $em,
        ValidatorInterface            $validator
    ): Response
    {
        $violations = $validator->validate($mangaRequest);

        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $manga->setTitle($mangaRequest->getTitle());

        $em->flush();

        return $this->json(
            $manga,
            Response::HTTP_OK,
            context: [
                "groups" => [
                    "manga:read"
                ]
            ]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Manga                  $manga,
        EntityManagerInterface $em,
    ): Response
    {
        $em->remove($manga);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
