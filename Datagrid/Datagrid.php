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
use UnZeroUn\Datagrid\Action\BatchAction;
use UnZeroUn\Datagrid\Datagrid\Form\Model\DatagridBatchAction;
use UnZeroUn\Datagrid\Datagrid\Form\Type\DatagridBatchActionType;
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
     * @var BatchAction[]
     */
    protected $batchActions = [];

    /**
     * @var FormInterface
     */
    private $batchActionForm;

    /**
     * @var FormView
     */
    private $batchActionFormView;

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
            $this->filterForm->submit($request->query->get($this->filterForm->getName()));
            $this->filterBuilderUpdater->addFilterConditions($this->filterForm, $qb);
        }

        if ($this->isPaginationEnabled()) {
            $this->getPager()->setCurrentPage($request->query->get('page', 1));
        }

        $batchActionForm = $this->getBatchActionForm();
        if (null !== $batchActionForm) {
            if ($batchActionForm->handleRequest($request) && $batchActionForm->isSubmitted()) {
                if ($batchActionForm->isValid()) {
                    /** @var DatagridBatchAction $data */
                    $data = $batchActionForm->getData();

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
     * @return BatchAction[]
     */
    public function getBatchActions(): array
    {
        return $this->batchActions;
    }

    public function getBatchActionForm(): ?FormInterface
    {
        if (null === $this->batchActionForm) {
            if (count($this->batchActions) === 0) {
                return null;
            }

            $this->batchActionForm = $this->formFactory->createNamed(
                'batch_action',
                DatagridBatchActionType::class,
                null,
                [
                    'items'   => $this->getResults(),
                    'actions' => $this->getBatchActions(),
                    'method'  => 'POST',
                ]
            );
        }

        return $this->batchActionForm;
    }

    public function getBatchActionFormView(): ?FormView
    {
        if (null === $this->batchActionFormView) {
            if (null === $this->getBatchActionForm()) {
                return null;
            }

            $this->batchActionFormView = $this->getBatchActionForm()->createView();
        }

        return $this->batchActionFormView;
    }

    public function getBatchActionItemForm($item): ?FormView
    {
        $formView = $this->getBatchActionFormView();

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
