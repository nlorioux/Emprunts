<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipment;
use App\Entity\User;
use App\Form\EquipmentType;
use App\Repository\BorrowingRepository;
use App\Repository\EquipmentRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ModeratorController extends AbstractController
{
    /**
     * @Route("/equipement/ajouter", name="equipment_add")
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
            'form' => $form->createView()
        ]);
    }
}
