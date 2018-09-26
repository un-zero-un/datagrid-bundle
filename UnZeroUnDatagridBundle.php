<?php

declare(strict_types=1);

namespace UnZeroUn\DatagridBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use UnZeroUn\DatagridBundle\Datagrid\DatagridType;

class UnZeroUnDatagridBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(DatagridType::class)->addTag('admin.datagrid.type');
    }

}