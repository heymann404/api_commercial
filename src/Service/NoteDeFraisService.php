<?php

namespace App\Service;

use App\Entity\Societe;
use App\Entity\TypeDeNote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NoteDeFraisService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function formatNoteDeFrais($noteDeFrais): array
    {
        return [
            'id' => $noteDeFrais->getId(),
            'dateDeLaNote' => $noteDeFrais->getDateDeLaNote()->format('Y-m-d'),
            'montant' => $noteDeFrais->getMontant(),
            'typeDeNote' => $noteDeFrais->getType()->getLibelle(),
            'societe' => $noteDeFrais->getSociete()->getNom(),
            'commercial' => [
                'id' => $noteDeFrais->getUser()->getId(),
                'nom' => $noteDeFrais->getUser()->getNom(),
                'prenom' => $noteDeFrais->getUser()->getPrenom(),
                'email' => $noteDeFrais->getUser()->getEmail()
            ],
            'dateDeCreation' => $noteDeFrais->getDateDeCreation()->format('Y-m-d\TH:i:s'),
            'dateDeModification' => $noteDeFrais->getDateDeModification()->format('Y-m-d\TH:i:s')
        ];
    }

    public function processNoteDeFraisRequest($requestData, $noteDeFrais) {

        if (!array_key_exists('codeTypeDeNote', $requestData)) {
            throw new BadRequestHttpException("Veuillez spécifier le code du type de la note");
        }

        if (!array_key_exists('societe', $requestData)) {
            throw new BadRequestHttpException("Veuillez spécifier la société");
        }

        if (!array_key_exists('montant', $requestData)) {
            throw new BadRequestHttpException("Veuillez spécifier le montant");
        }

        if (!array_key_exists('dateDeLaNote', $requestData)) {
            throw new BadRequestHttpException("Veuillez spécifier la dateDeLaNote");
        }

        $codeTypeDeNote = $requestData['codeTypeDeNote'];
        $typeDeNote = $this->em->getRepository(TypeDeNote::class)->findOneBy(['code' => $codeTypeDeNote]);
        if (!$typeDeNote) {
            throw new NotFoundHttpException("Aucun type de note trouvé pour le code $codeTypeDeNote");
        }
        $noteDeFrais->setType($typeDeNote);

        $idSociete = $requestData['societe'];
        $societe = $this->em->getRepository(Societe::class)->find($idSociete);
        if (!$societe) {
            throw new NotFoundHttpException("Aucune société trouvée pour l'id $idSociete");
        }
        $noteDeFrais->setSociete($societe);

        $montant = $requestData['montant'];
        if (!is_numeric($montant)) {
            throw new BadRequestHttpException("Veuillez spécifier un chiffre valide pour le montant de la note");
        }
        $noteDeFrais->setMontant($montant);

        $dateDeLaNote = date_create_from_format("Y-m-d", $requestData['dateDeLaNote']);
        if (!$dateDeLaNote) {
            throw new BadRequestHttpException("Veuillez spécifier la date de la note sous le format aaaa-mm-jj");
        }
        $noteDeFrais->setDateDeLaNote($dateDeLaNote);

        return $noteDeFrais;
    }
}