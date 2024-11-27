<?php

namespace App\Controller;

use App\Domain\Car\DTO\CarFilterDTO;
use App\Entity\Car;
use App\Form\CarFilterType;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/Voitures')]
final class CarController extends AbstractController
{
    #[Route(name: 'car_index', methods: ['GET'])]
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

        return $this->render('car/index.html.twig', [
            'cars' => $carsPagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'car_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->get('car');
        $type = $data['type'] ?? null;

        $car = new Car();
        $form = $this->createForm(CarType::class, $car, [
            'action' => $this->generateUrl('car_new'),
            'type' => $type
        ]);
        $form->handleRequest($request);

        if (!$request->isXmlHttpRequest() && $form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('success', 'Voiture crée 🎉');

            return $this->redirectToRoute('car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->render('car/show.html.twig', [
            'car' => $car,
        ]);
    }

    #[Route('/{id}/edit', name: 'car_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        $data = $request->get('car');
        $type = $data['type'] ?? $car->getType();

        if ($type !== null) {
            $car->setType($type);
        }

        $form = $this->createForm(CarType::class, $car, [
            'action' => $this->generateUrl('car_edit', ['id' => $car->getId()]),
            'type' => $type,
        ]);
        $form->handleRequest($request);

        if (!$request->isXmlHttpRequest() && $form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Voiture modifiée 📝');

            return $this->redirectToRoute('car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'car_delete', methods: ['POST'])]
    public function delete(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$car->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($car);
            $entityManager->flush();

            $this->addFlash('success', 'Voiture à bien été supprimée 🗑️');
        }

        return $this->redirectToRoute('car_index', [], Response::HTTP_SEE_OTHER);
    }
}
