<?php

namespace App\Twig\Runtime;

use App\Repository\NftRepository;
use Twig\Extension\RuntimeExtensionInterface;

class NftExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private NftRepository $nftRepository
        )
    {
        
    }

    public function getLastFiveNft()
    {
        return $this->nftRepository->findLastFiveNft();
    }
}
