<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\User;
use App\Entity\Adress;
use App\Form\UserType;
use App\Form\UserSearchType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private PaginatorInterface $paginator
    ){

    }
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $qb = $userRepository->getQbAll();

        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['userEmail'] !== null) {
                $qb->andwhere('u.email LIKE :email')
                ->setParameter('email', '%'. $data['userEmail'] .'%');
            }
        }
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1), // réxupérer le get
            5                                  // nbr element par page
        );


        return $this->render('user/index.html.twig', [
            'users' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('avatar')->getData();

            if($file !== null) {
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('upload_file'),
                        $newFileName
                    );
                    $user->setAvatar($newFileName);
                } catch (FileException $e){
                }
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('avatar')->getData();

            if($file !== null) {
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('upload_file'),
                        $newFileName
                    );
                    $user->setAvatar($newFileName);
                } catch (FileException $e){
    
                }

            }
            //AVANT
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/profil', name: 'app_user_profil_edit')]
    public function profil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); 
        }
    
        return $this->render('user/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/user', name: 'api_user_index', methods: ['GET'])]
    public function apiIndex(UserRepository $userRepository)
    {
        return $this->json($userRepository->findAll(), 200, context: ['groups' => 'user']);
    }

    #[Route('/api/user/{id}', name: 'api_user_id', methods: ['GET'])]
    public function apiId(User $user)
    {
        return $this->json($user, context: ['groups' => 'user']);
    }

    #[Route('/api/user/', name: 'api_user_id', methods: ['POST'])]
    public function apiEdit(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        try {
        $requestData = json_decode($request->getContent(), true);
        // var_dump($requestData);

        $user = new User();

        $datePost = $requestData['birth'];
        $datePost = date_parse_from_format('j/n/Y', $datePost);
        $date = new DateTime();
        $date->setDate($datePost['year'], $datePost['month'], $datePost['day']);

        
        $user->setEmail($requestData['email']);
        $user->setPassword($requestData['password']);
        $hashedPassword = $userPasswordHasher->hashPassword(
            $user,
            $requestData['password']
        );
        $user->setPassword($hashedPassword);
        $user->setPseudo($requestData['pseudo']);
        $user->setFirstName($requestData['firstName']);
        $user->setLastName($requestData['lastName']);
        $user->setRoles(['ROLE_USER']);
        $user->setBirthDate($date);
        $user->setPhoneNumber($requestData['phoneNumber']);
        $user->setAvatar($requestData['avatar']);
        $user->setGender($requestData['gender']);
    
        $addressData = $requestData['adresses'][0];
        $adress = new Adress();
        $adress->setCity($addressData['city']);
        $adress->setZipCode($addressData['zipCode']);
        $adress->setStreet($addressData['street']);
        $adress->setCountry($addressData['country']);

        $entityManager->persist($user);
        $entityManager->persist($adress);
        $entityManager->flush();
    
        return new JsonResponse($user . ' : 201 Created', Response::HTTP_CREATED, [], true);
        } catch (Exception $e) {
        return new JsonResponse([
            'message' => $e->getMessage()],
            Response::HTTP_BAD_REQUEST
        );
        }
    }
}