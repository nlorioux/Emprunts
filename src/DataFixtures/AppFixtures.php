<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Equipment;
use App\Entity\Borrowing;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        for($i = 1;$i<=10;$i++){
            $user = new User();
            $user->setUsername("username $i")
                ->setFirstName("Prénom $i")
                ->setLastName("Nom $i");
            $password = $this->encoder->encodePassword($user,"mdp $i");
            $user->setPassword($password)
                ->setEmail("username$i@ec-m.fr")
                ->setUid("2019$i$i$i$i");
                if($i==1) {
                    $user->setRoles(['ROLE_ADMIN']);
                } else {
                    $user->setRoles(['ROLE_USER']);
                }
            for($k = 1;$k<=2;$k++){
                $equipment = new Equipment();
                $equipment->setName("Materiel $i$k")
                    ->setDescription("Ce matériel $i$k est super cool!")
                    ->setQuantity("$k")
                    ->setAvailableStock($k-1)
                    ->setAllowedDays("3")
                    ->setUid("$k$k$k");
                $manager->persist($equipment);
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
            $manager->persist($user);
        }

        $manager->flush();
    }
}
