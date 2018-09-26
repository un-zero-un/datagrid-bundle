<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Accessor;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class PropertyPathAccessor implements Accessor
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var string
     */
    private $propertyPath;

    public function __construct(string $propertyPath, PropertyAccessor $propertyAccessor = null)
    {
        $this->propertyPath = $propertyPath;

        if (null === $propertyAccessor) {
            $propertyAccessor = new PropertyAccessor();
        }
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getValue($object)
    {
        try {
            return $this->propertyAccessor->getValue($object, $this->propertyPath);
        } catch (\Exception $e) {
        }

        return null;
    }

}
