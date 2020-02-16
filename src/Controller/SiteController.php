<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/connection", name="connection")
     */
    public function index()
    {
        return $this->render('site/connection.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(EquipmentRepository $repo)
    {
        $equipements = $repo->findAll();

        return $this->render('site/home.html.twig',[
            'equipements'=>$equipements]);
    }
}
