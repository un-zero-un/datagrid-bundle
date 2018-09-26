<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Accessor;

class CallableAccessor implements Accessor
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function getValue($object)
    {
        return call_user_func_array($this->callable, [$object]);
    }

}
