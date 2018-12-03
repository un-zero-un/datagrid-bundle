<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Route\Parameters;

class StaticRouteParameters implements RouteParameters
{
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function resolve(...$args): array
    {
        return $this->parameters;
    }

}
