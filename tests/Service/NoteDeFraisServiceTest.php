<?php

namespace App\Tests\Service;

use App\Entity\NoteDeFrais;
use App\Entity\Societe;
use App\Entity\TypeDeNote;
use App\Entity\User;
use App\Service\NoteDeFraisService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use PHPUnit\Framework\TestCase;

class NoteDeFraisServiceTest extends TestCase
{
    /*
     * Test unitaire de la fonction formatNoteDeFrais
     */
    public function testFormatNoteDeFrais()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $noteDeFrais = new NoteDeFrais();
        $noteDeFrais->setId(1);
        $noteDeFrais->setDateDeLaNote(new \DateTime('2023-10-13'));
        $noteDeFrais->setMontant(111.99);

        $typeDeNote = new TypeDeNote();
        $typeDeNote->setLibelle('essence');
        $noteDeFrais->setType($typeDeNote);

        $societe = new Societe();
        $societe->setNom('societe 1');
        $noteDeFrais->setSociete($societe);

        $user = new User();
        $user->setId(1);
        $user->setNom('nom commercial');
        $user->setPrenom('prenom commercial');
        $user->setEmail('commercial@test.com');
        $noteDeFrais->setUser($user);

        $noteDeFrais->setDateDeCreation(new \DateTime('2023-10-13 10:00:00'));
        $noteDeFrais->setDateDeModification(new \DateTime('2023-10-13 11:00:00'));

        $expectedResult = [
            'id' => 1,
            'dateDeLaNote' => '2023-10-13',
            'montant' => 111.99,
            'typeDeNote' => 'essence',
            'societe' => 'societe 1',
            'commercial' => [
                'id' => 1,
                'nom' => 'nom commercial',
                'prenom' => 'prenom commercial',
                'email' => 'commercial@test.com'
            ],
            'dateDeCreation' => '2023-10-13T10:00:00',
            'dateDeModification' => '2023-10-13T11:00:00'
        ];

        $result = $noteDeFraisService->formatNoteDeFrais($noteDeFrais);

        $this->assertEquals($expectedResult, $result);
    }


    /*
     * Tests unitaires de la fonction processNoteDeFraisRequest()
     */

    // cas normal où le tout fonctionne
    public function testProcessNoteDeFraisRequestValid()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $typeDeNoteRepository = $this->createMock(ObjectRepository::class);
        $societeRepository = $this->createMock(ObjectRepository::class);

        $em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($typeDeNoteRepository, $societeRepository);

        $typeDeNote = new TypeDeNote();
        $typeDeNote->setCode('ESSENCE');
        $typeDeNoteRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'ESSENCE'])
            ->willReturn($typeDeNote);

        $societe = new Societe();
        $societe->setId(1);
        $societeRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($societe);

        $requestData = [
            'codeTypeDeNote' => 'ESSENCE',
            'societe' => 1,
            'montant' => 115.6,
            'dateDeLaNote' => '2023-10-13'
        ];
        $dateDeLaNote = date_create_from_format("Y-m-d", $requestData['dateDeLaNote']);

        $noteDeFrais = new NoteDeFrais();
        $result = $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);

        $this->assertInstanceOf(NoteDeFrais::class, $result);
        $this->assertEquals($requestData['codeTypeDeNote'], $result->getType()->getCode());
        $this->assertEquals($requestData['societe'], $result->getSociete()->getId());
        $this->assertEquals($requestData['montant'], $result->getMontant());
        $this->assertEquals($dateDeLaNote, $result->getDateDeLaNote());
    }

    // cas où le code de la type de la note est manquant
    public function testProcessNoteDeFraisRequestMissingCodeTypeDeNote()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $requestData = [
            'societe' => 1,
            'montant' => 15,
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier le code du type de la note");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où l'id de la société est manquant
    public function testProcessNoteDeFraisRequestMissingSociete()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $requestData = [
            'codeTypeDeNote' => 'ESSENCE',
            'montant' => 15,
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier la société");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où le montant est manquant
    public function testProcessNoteDeFraisRequestMissingMontant()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $requestData = [
            'societe' => 1,
            'codeTypeDeNote' => 'ESSENCE',
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier le montant");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où la date de la note est manquante
    public function testProcessNoteDeFraisRequestMissingDateDeLaNote()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $requestData = [
            'montant' => 15,
            'societe' => 1,
            'codeTypeDeNote' => 'ESSENCE',
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier la dateDeLaNote");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où le type de note est invalide
    public function testProcessNoteDeFraisRequestInvalidTypeDeNote()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $typeDeNoteRepository = $this->createMock(ObjectRepository::class);

        $em->expects($this->once())
            ->method('getRepository')
            ->willReturn($typeDeNoteRepository);

        $typeDeNote = null;
        $typeDeNoteRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'INEXISTANT'])
            ->willReturn($typeDeNote);

        $requestData = [
            'codeTypeDeNote' => 'INEXISTANT',
            'societe' => 1,
            'montant' => 15,
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage("Aucun type de note trouvé pour le code INEXISTANT");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où la société est invalide
    public function testProcessNoteDeFraisRequestInvalidSociete()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $typeDeNoteRepository = $this->createMock(ObjectRepository::class);
        $societeRepository = $this->createMock(ObjectRepository::class);

        $em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($typeDeNoteRepository, $societeRepository);

        $typeDeNote = new TypeDeNote();
        $typeDeNoteRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'ESSENCE'])
            ->willReturn($typeDeNote);

        $societe = null;
        $societeRepository->expects($this->once())
            ->method('find')
            ->with(199)
            ->willReturn($societe);

        $requestData = [
            'codeTypeDeNote' => 'ESSENCE',
            'societe' => 199,
            'montant' => 15,
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage("Aucune société trouvée pour l'id 199");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    //cas où le montant est invalide
    public function testProcessNoteDeFraisRequestInvalidMontant()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $typeDeNoteRepository = $this->createMock(ObjectRepository::class);
        $societeRepository = $this->createMock(ObjectRepository::class);

        $em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($typeDeNoteRepository, $societeRepository);

        $typeDeNote = new TypeDeNote();
        $typeDeNoteRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'ESSENCE'])
            ->willReturn($typeDeNote);

        $societe = new Societe();
        $societeRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($societe);

        $requestData = [
            'codeTypeDeNote' => 'ESSENCE',
            'societe' => 1,
            'montant' => 'NaN',
            'dateDeLaNote' => '2023-10-13'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier un chiffre valide pour le montant de la note");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }

    // cas où la date de la note est invalide
    public function testProcessNoteDeFraisRequestInvalidDateDeLaNote()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $noteDeFraisService = new NoteDeFraisService($em);

        $typeDeNoteRepository = $this->createMock(ObjectRepository::class);
        $societeRepository = $this->createMock(ObjectRepository::class);

        $em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($typeDeNoteRepository, $societeRepository);

        $typeDeNote = new TypeDeNote();
        $typeDeNoteRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'ESSENCE'])
            ->willReturn($typeDeNote);

        $societe = new Societe();
        $societeRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($societe);

        $requestData = [
            'codeTypeDeNote' => 'ESSENCE',
            'societe' => 1,
            'montant' => 15,
            'dateDeLaNote' => '10-10-2023'
        ];

        $noteDeFrais = new NoteDeFrais();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Veuillez spécifier la date de la note sous le format aaaa-mm-jj");

        $noteDeFraisService->processNoteDeFraisRequest($requestData, $noteDeFrais);
    }
}
