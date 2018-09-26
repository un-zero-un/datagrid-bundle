<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use UnZeroUn\Datagrid\Datagrid\DatagridType;

class UnZeroUnDatagridBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(DatagridType::class)->addTag('admin.datagrid.type');
    }

}