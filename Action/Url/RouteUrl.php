<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteUrl implements Url
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var array|callable
     */
    private $routeParameters;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                string $routeName,
                                $routeParameters = [])
    {
        if (!is_array($routeParameters) && !is_callable($routeParameters)) {
            throw new \InvalidArgumentException('$routeParameters must be an array or a callable');
        }

        $this->urlGenerator    = $urlGenerator;
        $this->routeName       = $routeName;
        $this->routeParameters = $routeParameters;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getUrl(...$args): string
    {
        $routeParameters = [];

        if (is_array($this->routeParameters)) {
            $routeParameters = $this->routeParameters;
        } elseif (is_callable($this->routeParameters)) {
            $routeParameters = call_user_func_array($this->routeParameters, $args);

            if (!is_array($routeParameters)) {
                throw new \Exception('$routeParameters callable must return an array');
            }
        }

        return $this->urlGenerator->generate($this->routeName, $routeParameters);
    }

}
