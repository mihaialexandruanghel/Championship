<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomePageController extends AbstractController
{

     /**
      * @Route("/homepage", name="homepage")
      */
    public function number()
    {
        $number = random_int(0, 100);

        return $this->render('homepage/homepage.html.twig', [
            'number' => $number,
        ]);
    }

}
