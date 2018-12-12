<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

interface Url
{
    public function getUrl(array $context = []): string;
}
