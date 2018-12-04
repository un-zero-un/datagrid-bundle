<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid\Form\Model;

use UnZeroUn\Datagrid\Action\MassAction;

class DatagridMassAction
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var MassAction
     */
    private $action;

    public function __construct()
    {
        $this->items = [];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getAction(): ?MassAction
    {
        return $this->action;
    }

    public function setAction(MassAction $action): void
    {
        $this->action = $action;
    }
}
