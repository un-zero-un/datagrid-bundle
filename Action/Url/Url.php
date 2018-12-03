<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

interface Url
{
    public function getUrl(...$args): string;
}
