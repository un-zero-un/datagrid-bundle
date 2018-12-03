<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Route\Parameters;

interface RouteParameters
{
    public function resolve(...$args): array;
}
