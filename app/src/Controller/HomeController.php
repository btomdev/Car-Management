<?php

namespace App\Controller;

use App\Domain\Car\DTO\CarFilterDTO;
use App\Form\CarFilterType;
use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(Request $request, CarRepository $carRepository): Response
    {
        $carFilterDTO = new CarFilterDTO();
        $form = $this->createForm(CarFilterType::class, $carFilterDTO);
        $form->handleRequest($request);

        $page = $request->query->getInt('page', 1);
        $carsPagination = $carRepository->paginate($page, $carFilterDTO);
        $carsPagination->setCustomParameters([
            'align' => 'center',
            'size' => 'small',
            'rounded' => true,
        ]);

        return $this->render('home/index.html.twig', [
            'cars' => $carsPagination,
            'form' => $form->createView(),
        ]);
    }
}
