<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action;

use Symfony\Component\HttpFoundation\Response;
use UnZeroUn\Datagrid\Action\Url\Url;

class MassAction
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var callable
     */
    private $processor;

    public function __construct(string $label, callable $processor)
    {
        $this->label     = $label;
        $this->processor = $processor;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function process(array $items): ?Response
    {
        return call_user_func_array($this->processor, [$items]);
    }

}
