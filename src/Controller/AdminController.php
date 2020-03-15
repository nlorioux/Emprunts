<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Repository\BorrowingRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_panel")
     */
    public function administrationPanel(BorrowingRepository $borrowing_repo, EquipmentRepository $equipment_repo)
    {
        $nb_borrowings = $borrowing_repo->countAllInProgress();
        $nb_equipment = $equipment_repo->countAll();

        return $this->render('admin/administration.html.twig', [
            'nb_in_progress' => $nb_borrowings,
            'nb_equipment' =>$nb_equipment
        ]);
    }
}
