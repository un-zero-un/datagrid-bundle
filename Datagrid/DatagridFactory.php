<?php

declare(strict_types=1);

namespace UnZeroUn\DatagridBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Form\FormFactoryInterface;
use UnZeroUn\DatagridBundle\Exception\TypeNotFoundException;
use UnZeroUn\Sorter\SorterFactory;

class DatagridFactory
{
    /**
     * @var array
     */
    private $types;

    /**
     * @var SorterFactory
     */
    private $sorterFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FilterBuilderUpdater
     */
    private $filterBuilderUpdater;

    public function __construct(
        SorterFactory $sorterFactory,
        FormFactoryInterface $formFactory,
        FilterBuilderUpdater $filterBuilderUpdater,
        iterable $types
    )
    {
        $this->types                = $types;
        $this->sorterFactory        = $sorterFactory;
        $this->formFactory          = $formFactory;
        $this->filterBuilderUpdater = $filterBuilderUpdater;
    }

    public function createBuilder(QueryBuilder $queryBuilder): DatagridBuilder
    {
        return new DatagridBuilder($this->sorterFactory, $this->formFactory, $this->filterBuilderUpdater, $queryBuilder);
    }

    public function create(string $datagridType): Datagrid
    {
        foreach ($this->types as $type) {
            if (get_class($type) !== $datagridType) {
                continue;
            }

            $builder = $this->createBuilder($type->getQueryBuilder());
            $type->build($builder);

            return $builder->getDatagrid();
        }

        throw new TypeNotFoundException;
    }
}
