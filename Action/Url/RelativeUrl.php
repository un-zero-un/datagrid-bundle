<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

class RelativeUrl implements Url
{
    /**
     * @var string
     */
    private $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getUrl(...$args): string
    {
        return $this->getUri();
    }
}
