<?php

namespace App\Command;

use DateTime;
use DateTimeZone;
use App\Entity\Eth;
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
    name: 'app:get-eth-price',
    description: 'Get eth price',
)]
class GetEthPriceCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Eth $eth,
        private EntityManagerInterface $entityManager
    ){
        parent::__construct();
    }
    
    protected function configure(): void
    {

        $this
            ->setDescription('Get the price of ETH from CryptoCompare API')
            ->setHelp('This command allows you to fetch the current price of ETH from CryptoCompare API');
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        try{
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://min-api.cryptocompare.com/data/price', [
            'query' => [
                'fsym' => 'ETH',
                'tsyms' => 'EUR',
            ],
        ]);

        // set value in database
        $data = $response->toArray();
        $ethPriceEUR = $data['EUR'];
        
        $EthEntryPrice = new Eth();
        $convertInCents = $ethPriceEUR*100;

        $timezoneParis = new DateTimeZone('Europe/Paris');
        $dateTimeParis = new DateTime('now', $timezoneParis);

        $EthEntryPrice->setCurrentPrice($convertInCents);
        $EthEntryPrice->setUpdateDate($dateTimeParis);


        $this->entityManager->persist($EthEntryPrice);
        $this->entityManager->flush();

        $output->writeln('<fg=green>Current ETH price in EUR: ' . $ethPriceEUR . ' €</>');
        
        return Command::SUCCESS;
    } catch (\Exception $e) {

        $output->writeln('<fg=red>Error: Unable to fetch ETH price from CryptoCompare API.</>');
        $output->writeln('<fg=red>Reason: ' . $e->getMessage()).'</>';

        return Command::FAILURE;
    }
    }
}
