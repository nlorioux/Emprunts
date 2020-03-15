<?php

namespace App\Controller;

use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipment;
use App\Form\EquipmentType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ModeratorController extends AbstractController
{
    /**
     * @Route("/ajouter", name="equipment_add")
     */
    public function addEquipment(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $equipment = new Equipment();
        $form =$this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $equipment->setUid("2019".mt_rand(0,9999))
                ->setAvailableStock($equipment->getQuantity());

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // store the image file instead of its content
                $equipment->setImage($newFilename);
            }

            $manager->persist($equipment);
            $manager->flush();
            $this->addFlash(
                'success',
                'Le nouvel équipement a bien été enregistré !'
            );
            return $this->redirectToRoute('equipment_show',['id' => $equipment->getId()]);
        }

        return $this->render('equipment/equipmentAdd.html.twig', [
            'form' => $form->createView(),
            'add' => True
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="equipment_delete")
     */
    public function deleteEquipment(Equipment $equipment, BorrowingRepository $borrowing_repo){

        $manager = $this->getDoctrine()->getManager();
        $borrowings = $borrowing_repo->findBy(
            ['equipment' => $equipment]
        );
        foreach($borrowings as $borrowing){
            $manager->remove($borrowing);
            $manager->flush();
        }

        $manager->remove($equipment);
        $manager->flush();
        $this->addFlash(
            'warning',
            'L\'équipement a bien été supprimé.'
        );
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/modifier/{id}", name="equipment_edit")
     */
    public function editEquipment(Request $request, Equipment $equipment){
        $manager = $this->getDoctrine()->getManager();
        $old_quantity = $equipment->getQuantity();
        $old_available = $equipment->getAvailableStock();
        $borrowed = $old_quantity-$old_available;
        $form =$this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $equipment->setAvailableStock($equipment->getQuantity()-$borrowed);

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // store the image file instead of its content
                $equipment->setImage($newFilename);
            }

            $manager->persist($equipment);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'équipement a bien été modifié !'
            );
            return $this->redirectToRoute('equipment_show',['id' => $equipment->getId()]);
        }

        return $this->render('equipment/equipmentAdd.html.twig', [
            'form' => $form->createView(),
            'add' => False,
            'equipment' => $equipment
        ]);
    }
}
