<?php

namespace App\Command;

use DateTime;
use DateTimeZone;
use App\Entity\Solana;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:get-sol-price',
    description: 'Add a short description for your command',
)]
class GetSolPriceCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Solana $solana,
        private EntityManagerInterface $entityManager
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setDescription('Get the price of SOL from CryptoCompare API')
        ->setHelp('This command allows you to fetch the current price of SOL from CryptoCompare API');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try{
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', 'https://min-api.cryptocompare.com/data/price', [
                'query' => [
                    'fsym' => 'SOL',
                    'tsyms' => 'EUR',
                ],
            ]);
    
            // set value in database
            $data = $response->toArray();
            $solPriceEUR = $data['EUR'];
            
            $solEntryPrice = new Solana();
            $convertInCents = $solPriceEUR*100;
    
            $timezoneParis = new DateTimeZone('Europe/Paris');
            $dateTimeParis = new DateTime('now', $timezoneParis);
    
            $solEntryPrice->setCurrentPrice($convertInCents);
            $solEntryPrice->setUpdateDate($dateTimeParis);
    
    
            $this->entityManager->persist($solEntryPrice);
            $this->entityManager->flush();
    
            $output->writeln('<fg=green>Current SOL price in EUR: ' . $solPriceEUR . ' €</>');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
    
            $output->writeln('<fg=red>Error: Unable to fetch SOL price from CryptoCompare API.</>');
            $output->writeln('<fg=red>Reason: ' . $e->getMessage()).'</>';
    
            return Command::FAILURE;
        }
    }
}
