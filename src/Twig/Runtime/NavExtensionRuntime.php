<?php

namespace App\Twig\Runtime;

use App\Repository\TypeBienRepository;
use Twig\Extension\RuntimeExtensionInterface;

class NavExtensionRuntime implements RuntimeExtensionInterface
{
    private $typeBienRepository;

    public function __construct(TypeBienRepository $typeBienRepository)
    {
        $this->typeBienRepository = $typeBienRepository;
    }

    public function menuItems()
    {
        // On peux maintenant appeler la méthode getCountGamebyConsole
        return $this->typeBienRepository->getCountTypebienByBien();
    }

    public function dateOpen(int $dOpen)
    {
        $dateOpen = date('m/d/Y', $dOpen);
        return $dateOpen;
    }

    public function dateClose(int $dClose)
    {
        $dateClose = date('m/d/Y', $dClose);
        return $dateClose;
    }
}
