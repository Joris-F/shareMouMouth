<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Repository\CommentairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaires')]
class CommentairesController extends AbstractController
{
    #[Route('/', name: 'commentaires_index')]
    public function index(CommentairesRepository $commentairesRepository): JsonResponse
    {
        $commentaires = $commentairesRepository->findAll();
        $index        = 0;
        $jsonReturn   = [];
        foreach ($commentaires as $commentaire) {
            $jsonReturn[$index] = $commentaire->getToJson();
            $index++;
        }
        return new JsonResponse($jsonReturn);
    }

    #[Route('/new', name: 'commentaires_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        $commentaire = new Commentaires();
        $jsonReturn  = [];

        if (isset($requestContent['text']) && trim($requestContent['text']) != "") {
            $commentaire->setText($requestContent['text']);
        } else {
            $jsonReturn["errors"]["textEmpty"] = "Le commentaire ne peux pas être vide !";
        }
        if (isset($requestContent['idUser']) && is_numeric($requestContent['idUser'])) {
            $commentaire->setIdUser($requestContent['idUser']);
        } else {
            $jsonReturn["errors"]["idUserEmpty"] = "L'id de l'utilisateur écrivant ce commentaire doit être renseigné correctement !";
        }
        if (isset($requestContent['idPost']) && is_numeric($requestContent['idPost'])) {
            $commentaire->setIdPost($requestContent['idPost']);
        } else {
            $jsonReturn["errors"]["idPostEmpty"] = "L'id du post doit être renseigné correctement !";
        }

        if (array_key_exists("errors", $jsonReturn)) {
            return new JsonResponse($jsonReturn, 400);
        } else {

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $jsonReturn["response"] = "Commentaire create";
            return new JsonResponse($jsonReturn, 200);
        }
    }

    #[Route('/{id}/show', name: 'commentaires_show')]
    public function show(int $id, CommentairesRepository $commentairesRepository): JsonResponse
    {
        $commentaire = $commentairesRepository->find($id);
        if (!isset($commentaire)) {
            $jsonReturn["errors"]["idNotExist"] = "L'id mit en paramètre n'existe pas !";
            return new JsonResponse($jsonReturn, 400);
        } else {
            return new JsonResponse($commentaire->getToJson(), 200);
        }
    }

    #[Route('/{id}/edit', name: 'commentaires_edit')]
    public function edit(Request $request, int $id, CommentairesRepository $commentairesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commentaire = $commentairesRepository->find($id);

        $requestContent = json_decode($request->getContent(), true);

        $jsonReturn  = [];

        if (!isset($commentaire)) {
            $jsonReturn["errors"]["idNotExist"] = "L'id mit en paramètre n'existe pas !";
        } else {
            if (isset($requestContent['text']) && trim($requestContent['text']) != "") {
                $commentaire->setText($requestContent['text']);
            } else {
                $jsonReturn["errors"]["textEmpty"] = "Le commentaire ne peux pas être vide !";
            }
            if (isset($requestContent['idUser']) && is_numeric($requestContent['idUser'])) {
                $commentaire->setIdUser($requestContent['idUser']);
            } else {
                $jsonReturn["errors"]["idUserEmpty"] = "L'id de l'utilisateur écrivant ce commentaire doit être renseigné correctement !";
            }
            if (isset($requestContent['idPost']) && is_numeric($requestContent['idPost'])) {
                $commentaire->setIdPost($requestContent['idPost']);
            } else {
                $jsonReturn["errors"]["idPostEmpty"] = "L'id du post doit être renseigné correctement !";
            }
        }

        if (array_key_exists("errors", $jsonReturn)) {
            return new JsonResponse($jsonReturn, 400);
        } else {

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $jsonReturn["response"] = "Commentaire update";
            return new JsonResponse($jsonReturn, 200);
        }
    }

    #[Route('/{id}/delete', name: 'commentaires_delete')]
    public function delete(Request $request, int $id, CommentairesRepository $commentairesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commentaire = $commentairesRepository->find($id);
        if (!isset($commentaire)) {
            $jsonReturn["errors"]["idNotExist"] = "L'id mit en paramètre n'existe pas !";
            return new JsonResponse($jsonReturn, 400);
        } else {
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $jsonReturn["response"] = "Commentaire Delete";
            return new JsonResponse($jsonReturn, 200);
        }
    }
}
