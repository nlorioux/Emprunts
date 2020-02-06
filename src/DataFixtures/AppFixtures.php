<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Equipment;
use App\Entity\Borrowing;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1;$i<=15;$i++){
            $user = new User();
            $user->setUsername("username $i")
                ->setFirstName("Prénom $i")
                ->setLastName("Nom $i")
                ->setPassword("mdp $i")
                ->setEmail("username$i@ec-m.fr")
                ->setUid("2019$i$i$i$i");
                if($i==1) {
                    $user->setRoles(['ROLE_ADMIN']);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }
            $manager->persist($user);
        }

        for($i = 1;$i<=30;$i++){
            $equipment = new Equipment();
            $equipment->setName("Materiel $i")
                    ->setDescription("Ce matériel $i est super cool!")
                    ->setQuantity("$i")
                    ->setAvailableStock($i-1)
                    ->setAllowedDays("3")
                    ->setUid("$i$i$i");
            $manager->persist($equipment);
        }

        for($i = 1;$i<=40;$i++){
            $borrowing = new Borrowing();
            $borrowing->setLendBy($user)
                    ->setBorrowedBy($user)
                    ->setEquipment($equipment)
                    ->setStartedOn(new \DateTime())
                    ->setEndedOn(new \DateTime())
                    ->setAllowedDays("7")
                    ->setRemarks("Ce matériel avait déjà été abimé par un gros rageux.");
            $manager->persist($borrowing);
        }

        $manager->flush();
    }
}
