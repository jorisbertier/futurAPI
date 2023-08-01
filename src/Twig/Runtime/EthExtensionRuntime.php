<?php

namespace App\Twig\Runtime;

use App\Repository\EthRepository;
use Twig\Extension\RuntimeExtensionInterface;

class EthExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private EthRepository $ethRepository
    )
    {
        
    }

    public function getLastSevenEth()
    {
        return $this->ethRepository->findLastSevenEth();
    }
}
