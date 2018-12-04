<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action\Url;

class AbsoluteUrl implements Url
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(array $context = []): string
    {
        return $this->url;
    }

}
