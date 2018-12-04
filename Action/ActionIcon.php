<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action;

class ActionIcon
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $icon;

    public function __construct(string $type, string $icon)
    {
        $this->type = $type;
        $this->icon = $icon;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

}
