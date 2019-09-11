<?php

namespace App\Twig;


use App\Service\DateConnexionService;
use App\Service\StatService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class StatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nbrStats', [$this, 'readStats']),
            new TwigFunction('nbrConnexion', [$this, 'getAllDateConnexion']),
        ];
    }

    public function readStats()
    {
        $statService= New StatService();
        $statService->readStats();
    }

    public function getAllDateConnexion(){
        $dateConnexionService= New DateConnexionService();
        $dateConnexionService->getDateConnexion();
    }


}

