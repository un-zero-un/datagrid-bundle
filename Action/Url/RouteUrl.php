<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UnZeroUn\Datagrid\Action\Route\Parameters\RouteParameters;

class RouteUrl implements Url
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var null|RouteParameters
     */
    private $routeParameters;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                string $routeName,
                                ?RouteParameters $routeParameters = null)
    {
        $this->urlGenerator    = $urlGenerator;
        $this->routeName       = $routeName;
        $this->routeParameters = $routeParameters;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getRouteParamsResolver(): ?RouteParameters
    {
        return $this->routeParameters;
    }

    public function getUrl(...$args): string
    {
        $routeParameters = [];

        if (null !== $this->routeParameters) {
            $routeParameters = array_merge($routeParameters, $this->routeParameters->resolve(...$args));
        }

        return $this->urlGenerator->generate($this->routeName, $routeParameters);
    }

}
