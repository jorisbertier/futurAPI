<?php

namespace App\Controller;

use App\Entity\Eth;
use App\Form\EthType;
use App\Repository\EthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eth')]
class EthController extends AbstractController
{
    #[Route('/', name: 'app_eth_index', methods: ['GET'])]
    public function index(EthRepository $ethRepository): Response
    {
        return $this->render('eth/index.html.twig', [
            'eths' => $ethRepository->findAll(),
        ]);
    }

    #[Route('/courEth', name: 'app_cour_eth', methods: ['GET'])]
    public function courEth(): Response
    {
        return $this->render('eth/courEth.html.twig', [
            
        ]);
    }

    #[Route('/new', name: 'app_eth_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eth = new Eth();
        $form = $this->createForm(EthType::class, $eth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eth);
            $entityManager->flush();

            return $this->redirectToRoute('app_eth_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eth/new.html.twig', [
            'eth' => $eth,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eth_show', methods: ['GET'])]
    public function show(Eth $eth): Response
    {
        return $this->render('eth/show.html.twig', [
            'eth' => $eth,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_eth_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eth $eth, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EthType::class, $eth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eth_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eth/edit.html.twig', [
            'eth' => $eth,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eth_delete', methods: ['POST'])]
    public function delete(Request $request, Eth $eth, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eth->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eth);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eth_index', [], Response::HTTP_SEE_OTHER);
    }


}
