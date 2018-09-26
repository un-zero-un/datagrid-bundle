<?php

declare(strict_types=1);

namespace UnZeroUn\DatagridBundle\Accessor;

interface Accessor
{
    public function getValue($object);
}
