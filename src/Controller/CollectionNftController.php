<?php

namespace App\Controller;

use App\Entity\CollectionNft;
use App\Form\CollectionNftType;
use App\Form\CollectionNftSearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CollectionNftRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/collection/nft')]
class CollectionNftController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ){

    }

    #[Route('/', name: 'app_collection_nft_index', methods: ['GET'])]
    public function index(CollectionNftRepository $collectionNftRepository, Request $request): Response
    {
        $qb = $collectionNftRepository->getQbAll();

        $form = $this->createForm(CollectionNftSearchType::class);
        $form->handleRequest($request);   // écoute les globales

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['mediaCollectionNft'] !== null) {
                $qb->andwhere('c.label LIKE :collection')
                ->setParameter('collection', '%'. $data['mediaCollectionNft'] .'%');
            }
        }

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10                                  
        );
        return $this->render('collection_nft/index.html.twig', [
            // 'collection_nfts' => $collectionNftRepository->findAll(),
            'collection_nfts' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_collection_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collectionNft = new CollectionNft();
        $form = $this->createForm(CollectionNftType::class, $collectionNft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collectionNft);
            $entityManager->flush();

            return $this->redirectToRoute('app_collection_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collection_nft/new.html.twig', [
            'collection_nft' => $collectionNft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_nft_show', methods: ['GET'])]
    public function show(CollectionNft $collectionNft): Response
    {
        return $this->render('collection_nft/show.html.twig', [
            'collection_nft' => $collectionNft,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collection_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CollectionNft $collectionNft, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectionNftType::class, $collectionNft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_collection_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collection_nft/edit.html.twig', [
            'collection_nft' => $collectionNft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_nft_delete', methods: ['POST'])]
    public function delete(Request $request, CollectionNft $collectionNft, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collectionNft->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collectionNft);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collection_nft_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
