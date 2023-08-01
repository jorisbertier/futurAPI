<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Nft;
use App\Form\NftType;
use App\Form\NftSearchType;
use App\Entity\CollectionNft;
use App\Repository\NftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/nft')]
class NftController extends AbstractController
{
    public function __construct(
        private PaginatorInterface $paginator
    ){

    }


    #[Route('/', name: 'app_nft_index', methods: ['GET'])]
    public function index(NftRepository $nftRepository, Request $request): Response
    {
        $qb = $nftRepository->getQbAll();

        $form = $this->createForm(NftSearchType::class);
        $form->handleRequest($request);   // écoute les globales

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['nftTitle'] !== null) {
                $qb->andwhere('n.title LIKE :title')
                ->setParameter('title', '%'. $data['nftTitle'] .'%');
            }
        }

        // $nfts = $qb->getQuery()->getResult();

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1), // réxupérer le get
            10                                  // nbr element par page
        );


        return $this->render('nft/index.html.twig', [
            'nfts' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $nft = new Nft();

        $form = $this->createForm(NftType::class, $nft);
        $form->handleRequest($request);

        //upload file
        $uploadDirectory = $this->getParameter('upload_file');
        

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('filePath')->getData();

            if($file) {
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            }

            try{
                $file->move(
                    $this->getParameter('upload_file'),
                    $newFileName
                );
                $nft->setFilePath($newFileName);
            } catch (FileException $e){

            }

            $timezoneParis = new DateTimeZone('Europe/Paris');
            $dateTimeParis = new DateTime('now', $timezoneParis);
            $collection = new CollectionNft(); // Erreur ici si vous avez utilisé une ArrayCollection au lieu de CollectionNft
            $collection->addNft($nft);
    
            $nft->setDateCreation($dateTimeParis);
            $entityManager->persist($nft);
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nft/new.html.twig', [
            'nft' => $nft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nft_show', methods: ['GET'])]
    public function show(Nft $nft): Response
    {
        return $this->render('nft/show.html.twig', [
            'nft' => $nft,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nft $nft, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NftType::class, $nft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nft/edit.html.twig', [
            'nft' => $nft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nft_delete', methods: ['POST'])]
    public function delete(Request $request, Nft $nft, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nft->getId(), $request->request->get('_token'))) {
            $entityManager->remove($nft);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
    }
}
