<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid;

use UnZeroUn\Datagrid\Accessor\Accessor;
use UnZeroUn\Datagrid\Accessor\CallableAccessor;
use UnZeroUn\Datagrid\Accessor\PropertyPathAccessor;
use UnZeroUn\Datagrid\Action\Action;
use UnZeroUn\Datagrid\Action\BatchAction;
use UnZeroUn\Sorter\Definition;

final class DatagridBuilder extends Datagrid
{
    public function disablePagination()
    {
        $this->paginationEnabled = false;

        return $this;
    }

    public function enablePagination()
    {
        $this->paginationEnabled = true;

        return $this;
    }

    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function addColumn(string $name, string $label, $valueAccessor, ?string $type = null): self
    {
        $resolveAccessor = function ($accessor): Accessor {
            if ($accessor instanceof Accessor) {
                return $accessor;
            }

            if (is_string($accessor)) {
                return new PropertyPathAccessor($accessor);
            }

            if (is_callable($accessor)) {
                return new CallableAccessor($accessor);
            }

            throw new \InvalidArgumentException(
                'Argument 3 given to ' . __METHOD__ . ' must be a string, a callable or an instance of ' . Accessor::class
            );
        };

        $this->columns[$name] = new DatagridColumn(
            $name,
            $label,
            $resolveAccessor($valueAccessor),
            $type
        );

        return $this;
    }

    public function addAction(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    public function addGlobalAction(Action $action): self
    {
        $this->globalActions[] = $action;

        return $this;
    }

    public function addBatchAction(BatchAction $action): self
    {
        $this->batchActions[] = $action;

        return $this;
    }

    public function setSortDefinition(Definition $definition): self
    {
        $this->sortDefinition = $definition;

        return $this;
    }

    public function overrideSort(): self
    {
        $this->overrideSort = true;

        return $this;
    }

    public function appendSort(): self
    {
        $this->overrideSort = false;

        return $this;
    }

    public function setFilterType(string $filterType): self
    {
        $this->filterType = $filterType;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string|array $processStatusKey
     *
     * @return DatagridBuilder
     */
    public function setProcessStatusKey($processStatusKey): self
    {
        if (!is_string($processStatusKey) && !is_array($processStatusKey)) {
            throw new \InvalidArgumentException('$processStatusKey must be a string or an array of string');
        }

        if (!is_array($processStatusKey)) {
            $processStatusKey = [$processStatusKey];
        }

        $this->processStatusKey = $processStatusKey;

        return $this;
    }

    public function getDatagrid(): Datagrid
    {
        $datagrid                    = new Datagrid(
            $this->sorterFactory,
            $this->formFactory,
            $this->filterBuilderUpdater,
            $this->queryBuilder
        );
        $datagrid->columns           = $this->columns;
        $datagrid->sortDefinition    = $this->sortDefinition;
        $datagrid->title             = $this->title;
        $datagrid->filterType        = $this->filterType;
        $datagrid->actions           = $this->actions;
        $datagrid->paginationEnabled = $this->paginationEnabled;
        $datagrid->globalActions     = $this->globalActions;
        $datagrid->processStatusKey  = $this->processStatusKey;
        $datagrid->overrideSort      = $this->overrideSort;
        $datagrid->batchActions      = $this->batchActions;
        $datagrid->itemsPerPage      = $this->itemsPerPage;

        return $datagrid;
    }
}
