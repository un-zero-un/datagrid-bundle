<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Type;

class ImageDatagridType
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $alt;

    public function __construct(string $url, string $alt = '')
    {
        $this->url = $url;
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getAlt(): string
    {
        return $this->alt;
    }

}
