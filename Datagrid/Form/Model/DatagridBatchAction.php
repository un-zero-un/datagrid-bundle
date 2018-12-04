<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid\Form\Model;

use UnZeroUn\Datagrid\Action\BatchAction;

class DatagridBatchAction
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var BatchAction
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

    public function getAction(): ?BatchAction
    {
        return $this->action;
    }

    public function setAction(BatchAction $action): void
    {
        $this->action = $action;
    }
}
