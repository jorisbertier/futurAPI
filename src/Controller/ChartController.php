<?php

namespace App\Controller;

use App\Entity\Eth;
use App\Entity\Solana;
use App\Entity\BinanceCoin;
use App\Entity\Bitcoin;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chartEthPrices,
                ],
                [
                    'label' => 'Bitcoin Price',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chartBitcoinPrices,
                ],
                [
                    'label' => 'Binance Price',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chartBinanceCoinPrices,
                ],
                [
                    'label' => 'Solana Price',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chartSolanaPrices,
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
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $chartPrices,
                ],
            ],
        ]);
    
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => max($chartPrices), // Utilisez la valeur maximale pour définir la limite supérieure de l'axe Y
                ],
            ],
        ]);
    
        return $this->render('chart/courEth.html.twig', [
            'chart' => $chart,
        ]);
    }
}
