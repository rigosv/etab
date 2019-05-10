<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(RouterInterface $container)
    {
        $this->router = $container;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('has_route', [$this, 'routeExists']),
        ];
    }

    public function routeExists(string $name) : bool
    {

        return (null !== $this->router->getRouteCollection()->get($name));
    }
}