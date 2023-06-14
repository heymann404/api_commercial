<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\NoteDeFrais;
use App\Service\NoteDeFraisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="api_")
 */
class NoteDeFraisController extends AbstractController
{
    /**
     * @Route("/noteDeFrais", name="note_de_frais_list", methods={"GET"})
     */
    public function list(EntityManagerInterface $em, NoteDeFraisService $noteDeFraisService): JsonResponse
    {
        $notesDeFrais = $em->getRepository(NoteDeFrais::class)->findAll();

        $data = [];

        foreach ($notesDeFrais as $noteDeFrais) {
            $data[] = $noteDeFraisService->formatNoteDeFrais($noteDeFrais);
        }

        return $this->json($data);
    }

    /**
     * @Route("/noteDeFrais/{id}", name="note_de_frais_show", methods={"GET"})
     */
    public function show(NoteDeFraisService $noteDeFraisService, EntityManagerInterface $em, $id): JsonResponse
    {
        $noteDeFrais = $em->getRepository(NoteDeFrais::class)->find($id);
        if (!$noteDeFrais) {
            return new JsonResponse("Aucune note de frais trouvée pour l'id $id", Response::HTTP_NOT_FOUND);
        }

        return $this->json($noteDeFraisService->formatNoteDeFrais($noteDeFrais));
    }

    /**
     * @Route("/noteDeFrais", name="note_de_frais_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, NoteDeFraisService $noteDeFraisService): JsonResponse
    {
        $noteDeFrais = new NoteDeFrais();

        $user = $this->getuser();
        $noteDeFrais->setUser($user);

        try {
            $noteDeFraisService->processNoteDeFraisRequest(json_decode($request->getContent(), true), $noteDeFrais);

            $em->persist($noteDeFrais);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->json($noteDeFraisService->formatNoteDeFrais($noteDeFrais));
    }

    /**
     * @Route("/noteDeFrais/{id}", name="note_de_frais_edit", methods={"PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $em, NoteDeFraisService $noteDeFraisService, $id): JsonResponse
    {
        $noteDeFrais = $em->getRepository(NoteDeFrais::class)->find($id);
        if (!$noteDeFrais) {
            return new JsonResponse("Aucune note de frais trouvée pour l'id $id", Response::HTTP_NOT_FOUND);
        }

        try {
            $noteDeFraisService->processNoteDeFraisRequest(json_decode($request->getContent(), true), $noteDeFrais);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
        return $this->json($noteDeFraisService->formatNoteDeFrais($noteDeFrais));
    }

    /**
     * @Route("/noteDeFrais/{id}", name="note_de_frais_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $em, NoteDeFraisService $noteDeFraisService, $id): JsonResponse
    {
        $noteDeFrais = $em->getRepository(NoteDeFrais::class)->find($id);
        if (!$noteDeFrais) {
            return new JsonResponse("Aucune note de frais trouvée pour l'id $id", Response::HTTP_NOT_FOUND);
        }
        $em->remove($noteDeFrais);
        $em->flush();

        return $this->json("La note de frais à l'id $id a été supprimée avec succès");
    }
}
