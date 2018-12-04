<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnZeroUn\Datagrid\Action\Action;
use UnZeroUn\Datagrid\Action\MassAction;
use UnZeroUn\Datagrid\Datagrid\Form\Model\DatagridMassAction;
use UnZeroUn\Datagrid\Datagrid\Form\Type\DatagridMassActionType;
use UnZeroUn\Sorter\Definition;
use UnZeroUn\Sorter\Sorter;
use UnZeroUn\Sorter\SorterFactory;

class Datagrid
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var DatagridColumn[]
     */
    protected $columns = [];

    /**
     * @var Definition
     */
    protected $sortDefinition;

    /**
     * @var SorterFactory
     */
    protected $sorterFactory;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $filterType;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FilterBuilderUpdater
     */
    protected $filterBuilderUpdater;

    /**
     * @var bool
     */
    protected $paginationEnabled = true;

    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var Action[]
     */
    protected $globalActions = [];

    /**
     * @var MassAction[]
     */
    protected $massActions = [];

    /**
     * @var FormInterface
     */
    private $massActionForm;

    /**
     * @var FormView
     */
    private $massActionFormView;

    /**
     * @var array
     */
    protected $processStatusKey;

    /**
     * @var Sorter
     */
    private $sorter;

    /**
     * @var bool
     */
    protected $overrideSort = true;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var FormInterface
     */
    private $filterForm;

    public function __construct(
        SorterFactory $sorterFactory,
        FormFactoryInterface $formFactory,
        FilterBuilderUpdater $filterBuilderUpdater,
        QueryBuilder $queryBuilder
    )
    {
        $this->queryBuilder         = $queryBuilder;
        $this->sorterFactory        = $sorterFactory;
        $this->formFactory          = $formFactory;
        $this->filterBuilderUpdater = $filterBuilderUpdater;
    }

    public function handleRequest(Request $request): ?Response
    {
        $qb           = $this->queryBuilder;
        $this->sorter = $this->sorterFactory->createSorter($this->sortDefinition);

        if (null !== $this->filterType) {
            $this->filterForm = $this->formFactory->create($this->filterType);
        }

        $this->sorter->handleRequest($request);
        $this->sorter->sort(
            $qb,
            [
                'override' => $this->overrideSort,
            ]
        );

        if (null !== $this->filterForm && $request->query->has($this->filterForm->getName())) {
            $this->filterForm->handleRequest($request);
            $this->filterBuilderUpdater->addFilterConditions($this->filterForm, $qb);
        }

        if ($this->isPaginationEnabled()) {
            $this->getPager()->setCurrentPage($request->query->get('page', 1));
        }

        $massActionForm = $this->getMassActionForm();
        if (null !== $massActionForm) {
            if ($massActionForm->handleRequest($request) && $massActionForm->isSubmitted()) {
                if ($massActionForm->isValid()) {
                    /** @var DatagridMassAction $data */
                    $data = $massActionForm->getData();

                    $response = $data->getAction()->process($data->getItems());

                    if ($response instanceof Response) {
                        return $response;
                    }
                }
            }
        }

        return null;
    }

    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function getResults(): iterable
    {
        if ($this->isPaginationEnabled()) {
            return $this->getPager()->getCurrentPageResults();
        }

        return $this->queryBuilder->getQuery()->getResult();
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return Action[]
     */
    public function getGlobalActions(): array
    {
        return $this->globalActions;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPager(): ?Pagerfanta
    {
        if ($this->isPaginationEnabled() && null === $this->pager) {
            $this->pager = new Pagerfanta(new DoctrineORMAdapter($this->queryBuilder));
            $this->pager->setMaxPerPage(25);
        }

        return $this->pager;
    }

    public function getSorter(): ?Sorter
    {
        return $this->sorter;
    }

    public function getFilterForm(): ?FormInterface
    {
        return $this->filterForm;
    }

    /**
     * @return MassAction[]
     */
    public function getMassActions(): array
    {
        return $this->massActions;
    }

    public function getMassActionForm(): ?FormInterface
    {
        if (null === $this->massActionForm) {
            if (count($this->massActions) === 0) {
                return null;
            }

            $this->massActionForm = $this->formFactory->createNamed(
                'mass_action',
                DatagridMassActionType::class,
                null,
                [
                    'items'   => $this->getResults(),
                    'actions' => $this->getMassActions(),
                    'method'  => 'POST',
                ]
            );
        }

        return $this->massActionForm;
    }

    public function getMassActionFormView(): ?FormView
    {
        if (null === $this->massActionFormView) {
            if (null === $this->getMassActionForm()) {
                return null;
            }

            $this->massActionFormView = $this->getMassActionForm()->createView();
        }

        return $this->massActionFormView;
    }

    public function getMassActionItemForm($item): ?FormView
    {
        $formView = $this->getMassActionFormView();

        if (null === $formView) {
            return null;
        }

        foreach ($formView['items'] as $formItem) {
            if ($formItem->vars['label'] === (string)$item) {
                return $formItem;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getProcessStatusKey(): ?array
    {
        return $this->processStatusKey;
    }
}
