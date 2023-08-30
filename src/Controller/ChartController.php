<?php

namespace App\Controller;

use App\Entity\Eth;
use App\Entity\Solana;
use App\Entity\Bitcoin;
use App\Entity\BinanceCoin;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/chart')]
class ChartController extends AbstractController
{
    #[Route('/', name: 'app_chart')]
    public function index(EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response
    {
        $ethData = $entityManager->getRepository(Eth::class)->findBy([], ['updateDate' => 'DESC'], 7);
        $solanaData = $entityManager->getRepository(Solana::class)->findBy([], ['updateDate' => 'DESC'], 7);
        $binanceCoinData = $entityManager->getRepository(BinanceCoin::class)->findBy([], ['updateDate' => 'DESC'], 7);
        $bitcoinData = $entityManager->getRepository(Bitcoin::class)->findBy([], ['updateDate' => 'DESC'], 7);

        $chartLabels = [];
        $chartEthPrices = [];
        $chartSolanaPrices = [];
        $chartBinanceCoinPrices = [];
        $chartBitcoinPrices = [];
    
        foreach ($ethData as $data) {
            $chartLabels[] = $data->getUpdateDate()->format('Y-m-d H:i:s');
            $chartEthPrices[] = $data->getCurrentPrice()/100;
        }

        foreach ($solanaData as $data) {
            $chartSolanaPrices[] = $data->getCurrentPrice()/100;
        }

        foreach ($binanceCoinData as $data) {
            $chartBinanceCoinPrices[] = $data->getCurrentPrice()/100;
        }

        foreach ($bitcoinData as $data) {
            $chartBitcoinPrices[] = $data->getCurrentPrice()/100;
        }
    
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
    
        $chart->setData([
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'ETH Price',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'data' => $chartEthPrices,
                    'tension' => 0.4,
                    'pointRadius' => 6
                ],
                [
                    'label' => 'Bitcoin Price',
                    'backgroundColor' => '#f2a900',
                    'borderColor' => '#f2a900',
                    'data' => $chartBitcoinPrices,
                    'tension' => 0.4,
                    'pointRadius' => 6
                ],
                [
                    'label' => 'Binance Price',
                    'backgroundColor' => '#0C0E12',
                    'borderColor' => '#0C0E12',
                    'data' => $chartBinanceCoinPrices,
                    'tension' => 0.4,
                    'pointRadius' => 6
                ],
                [
                    'label' => 'Solana Price',
                    'backgroundColor' => '#03E1FF',
                    'borderColor' => '#03E1FF',
                    'data' => $chartSolanaPrices,
                    'tension' => 0.4,
                    'pointRadius' => 6
                ],
            ],
        ]);
    
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => max($chartBitcoinPrices), // Utilize the maximum value for the Y-axis upper limit
                ],
            ],
        ]);
    
        return $this->render('chart/index.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/courEth', name: 'app_chart_eth', methods: ['GET'])]
    public function courEth(EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response
    {
        $ethData = $entityManager->getRepository(Eth::class)->findBy([], ['updateDate' => 'DESC'], 7);

        // Préparer les données pour le graphique
        $chartLabels = [];
        $chartPrices = [];
    
        foreach ($ethData as $data) {
            $chartLabels[] = $data->getUpdateDate()->format('Y-m-d H:i:s');
            $chartPrices[] = $data->getCurrentPrice()/100;
        }
    
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
    
        $chart->setData([
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'ETH Price',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'data' => $chartPrices,
                    'fill'=> false,
                    'tension' => 0.4,
                    'pointRadius' => 6,
                    

                ],
            ],
        ]);
    
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => max($chartPrices) . ' €', // Utilisez la valeur maximale pour définir la limite supérieure de l'axe Y
                ],
            ],
        ]);
    
        return $this->render('chart/courEth.html.twig', [
            'chart' => $chart,
        ]);
    }
}
