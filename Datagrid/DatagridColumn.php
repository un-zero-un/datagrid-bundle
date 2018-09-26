<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid;

use UnZeroUn\Datagrid\Accessor\Accessor;

class DatagridColumn
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var Accessor
     */
    private $accessor;

    /**
     * @var null|string
     */
    private $type;

    public function __construct(string $name, string $label, Accessor $accessor, ?string $type = null)
    {
        $this->name     = $name;
        $this->label    = $label;
        $this->accessor = $accessor;
        $this->type     = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return Accessor
     */
    public function getAccessor(): Accessor
    {
        return $this->accessor;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
