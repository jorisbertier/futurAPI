<?php

namespace App\Twig\Runtime;

use App\Repository\EthRepository;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private EthRepository $ethRepository)
    {
        // Inject dependencies if needed
    }

    public function getPriceActualEth()
    {
        return $this->ethRepository->findActualPrice();
    }
    public function calculatePriceEth($amount)
    {

        /**@var Eth $eth */
        $eth = $this->getPriceActualEth();
        return number_format($amount * $eth->getCurrentPrice() / 100, 2, ',', ' ');
    }

}
