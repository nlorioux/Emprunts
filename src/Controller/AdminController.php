<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Entity\User;
use App\Repository\BorrowingRepository;
use App\Repository\EquipmentRepository;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use PhpParser\Node\Scalar\String_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Class AdminController
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_panel")
     */
    public function administrationPanel(BorrowingRepository $borrowing_repo, EquipmentRepository $equipment_repo, UserRepository $user_repo)
    {
        $nb_borrowings = $borrowing_repo->countAllInProgress();
        $nb_equipment = $equipment_repo->countAll();

        $today = new \DateTime("now");
        $today->modify('- 1 day');
        $nb_late = $borrowing_repo->countAllLate($today);

        $nb_admin = $user_repo->countAdmin();

        $admins = $user_repo->getAdmin();

        return $this->render('admin/administration.html.twig', [
            'nb_in_progress' => $nb_borrowings,
            'nb_equipment' => $nb_equipment,
            'nb_late' => $nb_late,
            'nb_admin' => $nb_admin,
            'admins' => $admins
        ]);
    }

    /**
     * @Route("/destituer/{id}", name="admin_dismiss")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function dismissAdmin(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $user->setRoles(['ROLE_MEMBER']);
        $manager->persist($user);
        $manager->flush();

       return $this->redirectToRoute('admin_panel');
    }

    /**
     * @Route("/ajouter/", name="admin_add")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function addAdmin(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            -> add('user', EntityType::class,[
                'class'=> User::class,
                'choice_label' => function(User $user){ return $user->getFirstName().' '.$user->getLastName(); },
                'attr'=>["data-live-search"=>true],
                'placeholder'=>'Utilisateur'

            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN'
                ],
                'placeholder'=>'Role'
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData()['user'];
            $role = $form->getData()['roles'];
            $user->setRoles([$role]);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin_panel');
        }

        return $this->render('admin/adminAdd.html.twig',[
        'form'=>$form->createView(),
    ]);
    }
}
