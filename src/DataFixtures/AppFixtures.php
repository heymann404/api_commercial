<?php

namespace App\DataFixtures;

use App\Entity\NoteDeFrais;
use App\Entity\Societe;
use App\Entity\TypeDeNote;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typesDeNotes = [
            ['libelle' => 'essence', 'code' => 'ESSENCE'],
            ['libelle' => 'péage', 'code' => 'PEAGE'],
            ['libelle' => 'repas', 'code' => 'REPAS'],
            ['libelle' => 'conférence', 'code' => 'CONFERENCE']
        ];

        $createdTypesDeNotes = [];

        foreach ($typesDeNotes as $tdn) {
            $typeDeNote = new TypeDeNote();
            $typeDeNote->setLibelle($tdn['libelle']);
            $typeDeNote->setCode($tdn['code']);
            $manager->persist($typeDeNote);
            $createdTypesDeNotes[] = $typeDeNote;
        }

        $societe = new Societe();
        $societe->setNom('societe de test');
        $manager->persist($societe);

        $user = new User();
        $user->setNom('nom commercial');
        $user->setPrenom('prenom commercial');
        $user->setEmail('commercial1@email.com');
        $user->setPassword('$2y$13$lQunvo2Rh0gjGIbWCGWouO11dks60fYcMTIOacnrEPxVddPJMwSTa');
        $user->setDateDeNaissance(new \DateTime('1995-11-30'));
        $manager->persist($user);

        $noteDeFrais = new NoteDeFrais();
        $noteDeFrais->setSociete($societe);
        $noteDeFrais->setUser($user);
        $noteDeFrais->setMontant(100);
        $noteDeFrais->setType($createdTypesDeNotes[0]);
        $noteDeFrais->setDateDeLaNote(new \DateTime('2023-10-06'));
        $manager->persist($noteDeFrais);

        $manager->flush();
    }
}
