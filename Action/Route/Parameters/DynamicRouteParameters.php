<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Route\Parameters;

class DynamicRouteParameters implements RouteParameters
{
    /**
     * @var callable
     */
    private $resolver;

    public function __construct(callable $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve(...$args): array
    {
        return call_user_func_array($this->resolver, $args);
    }
}
