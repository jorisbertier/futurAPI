<?php

namespace App\Controller;

use DateTime;
use Normalizer;
use DateTimeZone;
use App\Entity\Nft;
use App\Entity\User;
use App\Form\NftType;
use App\Entity\Category;
use App\Form\NftSearchType;
use App\Entity\CollectionNft;
use Doctrine\ORM\EntityManager;
use App\Repository\EthRepository;
use App\Repository\NftRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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
    public function index(NftRepository $nftRepository, Request $request, EthRepository $ethRepository): Response
    {
        
        $qb = $nftRepository->getQbAll();

        $form = $this->createForm(NftSearchType::class);
        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['nftTitle'] !== null) {
                $qb->andwhere('n.title LIKE :title')
                ->setParameter('title', '%'. $data['nftTitle'] .'%');
            }
            if($data['dateCreation'] !== null) {
                $qb->andWhere('n.dateCreation > :dateCreation')
                ->setParameter('dateCreation', $data['dateCreation']);
            }
            if($data['orderByPrice'] === 'DESC') {
                $qb->andwhere('n.price IS NOT NULL')
                ->orderBy('n.price', 'DESC');
            } elseif($data['orderByPrice'] === 'ASC') {
                $qb->andwhere('n.price IS NOT NULL')
                ->orderBy('n.price', 'ASC');
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
            'actualPriceEth' => $ethRepository->findActualPrice()
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
            // $collection = new CollectionNft(); // Erreur ici si vous avez utilisé une ArrayCollection au lieu de CollectionNft
            // $collection->addNft($nft);
            
            // $nft->setCollection($collection);
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
    public function edit(Request $request, Nft $nft, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(NftType::class, $nft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('filePath')->getData();

            if($file) {
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('upload_file'),
                        $newFileName
                    );
                    $nft->setFilePath($newFileName);
                } catch (FileException $e){
                }
            }
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

    /////// API /////////

    #[Route('/api/nft', 'api_nft_index', methods: ['GET'])]
    public function apiNft(NftRepository $nftRepository)
    {
        return $this->json($nftRepository->findAll(), 200, context: ['groups' => 'nft', 'category', 'collection']);
    }

    #[Route('/api/nft/{id}', 'api_nft_id', methods: ['GET'])]
    public function apiNftId(Nft $nft)
    {
        return $this->json($nft, context: ['groups' => 'nft']);
    }

    #[Route('/api/nft', 'api_nft_new', methods: ['POST'])]
    public function apiNftNew(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, UserRepository $userRepository)
    {
        // dd($request->files->get("filePath"));
        
        
        // dd($token);
        
        try {
            $requestData = json_decode($request->getContent(), true);
            
            
            $nft = new Nft();
            
            $token = $request->headers->get('Authorization');
            $tokenParts = explode(".", $token);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);
                

            $timezoneParis = new DateTimeZone('Europe/Paris');
            $dateTimeParis = new DateTime('now', $timezoneParis);
        
        
            $nft->setDateCreation($dateTimeParis);
            $nft->setDescription($requestData['description']);
            $nft->setTitle($requestData['title']);
            $nft->setPrice($requestData['price']);
            $nft->setAlt($requestData['alt']);
            $user = $userRepository->findOneBy(['email' => $jwtPayload->username]);
            $nft->setUser($user);
            
            // $base64Image = $requestData['filePath']; // La base64 est dans le champ "filePath"
            // $fileName = $uploadService->uploadFile($base64Image);
            // $nft->setFilePath($fileName);

            $nft->setFilePath($requestData['filePath']);
            $category = new Category();
            $category->setLabel($requestData['category']);

            $collection = new CollectionNft();
            $collection->setLabel($requestData['collection']);

            // $user = new User();
            // $user->setPseudo($requestData['user']);
        
            // $nft->setUser($user);
            $nft->addCategory($category);
            $nft->setCollection($collection);
            // $entityManager->persist($user);
            $entityManager->persist($nft);
            $entityManager->persist($category);
            $entityManager->persist($collection);
            $entityManager->flush();
        
            return new JsonResponse($nft . ' : 201 Created', Response::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/api/nft/{id}', name: 'api_nft_edit', methods: ['PUT'])]
    public function apiNftEdit(Nft $nft, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);

            if (isset($requestData['description'])) {
                $nft->setDescription($requestData['description']);
            }
            if (isset($requestData['title'])) {
                $nft->setTitle($requestData['title']);
            }
            if (isset($requestData['price'])) {
                $nft->setPrice($requestData['price']);
            }
            if (isset($requestData['filePath'])) {
                $nft->setFilePath($requestData['filePath']);
            }
            if (isset($requestData['alt'])) {
                $nft->setAlt($requestData['alt']);
            }
            if (isset($requestData['category'])) {
                $category = new Category();
                $category->setLabel($requestData['category']);
            }
            if (isset($requestData['collection'])) {
                $collection = new CollectionNft();
                $collection->setLabel($requestData['collection']);
            }
            $entityManager->flush();

        return $this->json(['message' => 'NFT updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    #[Route('/api/delete/{id}', name: 'api_nft_delete', methods: ['DELETE'])]
    public function apiDeleteNft(Nft $nft, EntityManagerInterface $entityManager): JsonResponse {
        $entityManager->remove($nft);
        $entityManager->flush();
        return $this->json(['message' => 'NFT delete successfull'], Response::HTTP_NO_CONTENT);
    }


    #[Route('/api/naruto', 'api_nft_naruto', methods: ['GET'])]
    public function apiNarutoNft(NftRepository $nftRepository)
    {
        $nfts = $nftRepository->findLastSixNarutoNft();
        return $this->json($nfts, context: ['groups' => 'nft']);
    }

    
    // #[Route('/api/category', 'api_category')]
    // public function apiCategory(CategoryRepository $categoryRepository)
    // {
    //     $categories = $categoryRepository->findAll();
    //     return $this->json($categories, context: ['groups' => 'category']);
    // }
}


