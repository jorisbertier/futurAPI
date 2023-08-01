<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:set-admin',
    description: 'Set admin role to user',
)]
class SetAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('userEmail', InputArgument::REQUIRED, 'set admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $userEmail = $input->getArgument('userEmail'); //recperer email quand on tape la commande
        // $output->writeln($userEmail);   //écrit l'email
        $user= $this->userRepository->findOneBy(["email" => $userEmail]); // récupèrer un user complet et filtre
        // equivalent à  "SELECT * FROM user WHERE email = "" "

        if($user !== null ) {
            $user->setRoles(['ROLE_ADMIN']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln("Le rôle admin à etait donner à ".$user->getEmail()." et a été envoyé en bdd");
            return Command::SUCCESS;
        }
        $output->writeln("Utilisateur non existant");
        return Command::FAILURE;
    }
}
