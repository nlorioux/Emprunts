<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\User;
use App\Form\EquipmentType;
use App\Repository\BorrowingRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @IsGranted("ROLE_USER")
 */

class EquipmentController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(EquipmentRepository $repo, BorrowingRepository $repoborrowing)
    {
        $equipments = $repo->findAllOrderByStock();
        $user = $this->getUser();
        $user_borrowings = $repoborrowing->findBy([
            'borrowedBy' => $user,
            'inProgress' => True
        ]);

        $today = new \DateTime("now");
        $today->modify('- 1 day');
        foreach($user_borrowings as $user_borrowing){
            if($user_borrowing->getEndedOn() < $today){
                $this->addFlash('danger',
                    'Vous êtes en retard pour le rendu d\'un emprunt !');
            }
        }

        return $this->render('equipment/home.html.twig',[
            'equipments' => $equipments,
            'user' => $user,
            'user_borrowings' => $user_borrowings,
            'today' => $today
        ]);
    }


    /**
     * @Route("/equipement/{id}", name="equipment_show")
     */
    public function equipment(Equipment $equipment, BorrowingRepository $repo, PaginatorInterface $paginator, Request $request){

        $borrowings_query = $repo->findAllVisibleQuery($equipment);
        $borrowings = $paginator->paginate(
            $borrowings_query,
            $request->query->getInt('page',1),
            20
            );

        return $this->render('equipment/equipment.html.twig',[
            'equipment'=>$equipment,
            'borrowings'=>$borrowings
            ]);
    }


}
