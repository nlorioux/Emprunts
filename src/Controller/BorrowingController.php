<?php

namespace App\Controller;

use App\Form\BorrowingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipment;
use App\Entity\User;
use App\Entity\Borrowing;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("ROLE_MEMBER")
 */

class BorrowingController extends AbstractController
{
    /**
     * @Route("/emprunt/{id_equipment}", name="borrowing_add")
     * @ParamConverter("equipment", options={"id"="id_equipment"})
     */
    public function addBorrowing(Equipment $equipment, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $borrowing = new Borrowing();
        $borrowing->setAllowedDays($equipment->getAllowedDays())
            ->setStartedOn(new \DateTime("now"));
        $form = $this->createForm(BorrowingType::class, $borrowing);
        $form->handleRequest($request);

        $stock_dispo = $equipment->getAvailableStock();
        $param_request = $request->request->get('borrowing');
        $quantity_borrowed = $param_request['quantity'];
        $is_possible = ($stock_dispo >= $quantity_borrowed);
        if(! $is_possible){
            $this->addFlash(
                'danger',
                'Pas assez de stock disponible!'
            );
        }
        if($form->isSubmitted() && $form->isValid() && $is_possible){
            $borrowing->setEquipment($equipment)
                      ->setLendBy($user)
                      ->setInProgress(True);

            $nb_days = $borrowing->getAllowedDays();
            $start = $borrowing->getStartedOn();
            $end = clone $start;
            $borrowing->setEndedOn($end->modify('+'.$nb_days.'day'));
            $manager->persist($borrowing);

            $stock = $equipment->getAvailableStock();
            $quantity = $borrowing->getQuantity();
            $equipment->setAvailableStock($stock-$quantity);
            $manager->flush();

            return $this->redirectToRoute('equipment_show',[
                'id'=>$equipment->getId()
            ]);

        }

        return $this->render('borrowing/borrowingAdd.html.twig', [
            'form'=>$form->createView(),
            'equipment'=>$equipment
        ]);
    }

    /**
     * @Route("/rendu/{id_borrowing}", name="borrowing_end")
     * @ParamConverter("borrowing", options={"id"="id_borrowing"})
     */
    public function endBorrowing(Borrowing $borrowing, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($borrowing)
                        ->add('endedOn', DateType::class,[
                            'widget' => 'single_text'
                        ])
                        ->add('remarks', null, ['attr' =>[
                            'placeholder'=> 'Remarques'
                        ]])
                        ->getForm();
        $form->handleRequest($request);
        $equipment = $borrowing->getEquipment();

        if($form->isSubmitted() && $form->isValid()){
            $borrowing->setInProgress(False);
            $manager->persist($borrowing);

            $stock = $equipment->getAvailableStock();
            $quantity = $borrowing->getQuantity();
            $equipment->setAvailableStock($stock+$quantity);
            $manager->persist($equipment);
            $manager->flush();

            return $this->redirectToRoute('equipment_show',[
                'id' => $equipment->getId()
            ]);
        }

        return $this->render('borrowing/borrowingEnd.html.twig',[
            'form' => $form->createView(),
            'equipment' => $equipment
        ]);
    }
}
