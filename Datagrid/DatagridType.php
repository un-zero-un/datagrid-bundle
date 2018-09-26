<?php

declare(strict_types=1);

namespace UnZeroUn\DatagridBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;

interface DatagridType
{
    public function build(DatagridBuilder $builder): void;

    public function getQueryBuilder(): QueryBuilder;
}
