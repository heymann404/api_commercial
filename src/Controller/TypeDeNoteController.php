<?php

namespace App\Controller;

use App\Entity\TypeDeNote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="api_")
 */
class TypeDeNoteController extends AbstractController
{
    /**
     * @Route("/typeDeNote", name="type_de_note_list", methods={"GET"})
     */
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $typesDeNotes = $em->getRepository(TypeDeNote::class)->findAll();

        $data = [];

        foreach ($typesDeNotes as $typeDeNotes) {
            $data[] = [
                'libellé' => $typeDeNotes->getLibelle(),
                'code' => $typeDeNotes->getCode()
            ];
        }

        return $this->json($data);
    }
}
