<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Twig\Extension;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use UnZeroUn\Datagrid\Action\ActionIcon;
use UnZeroUn\Datagrid\Datagrid\Datagrid;
use UnZeroUn\Datagrid\Datagrid\DatagridColumn;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\InitRuntimeInterface;
use Twig\TwigFunction;
use Twig_Environment;

class DatagridExtension extends AbstractExtension implements InitRuntimeInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var UnderscoreNamingStrategy
     */
    private $classNamingStrategy;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor    = $propertyAccessor;
        $this->classNamingStrategy = new UnderscoreNamingStrategy();
    }

    public function initRuntime(Twig_Environment $environment)
    {
        $this->twig = $environment;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('datagrid', [$this, 'displayDatagrid'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_row', [$this, 'displayRow'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_cell', [$this, 'displayCell'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_cell_value', [$this, 'displayCellValue'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_action_attributes', [$this, 'displayActionAttributes'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_header', [$this, 'displayHeader'], ['is_safe' => ['html']]),
            new TwigFunction('datagrid_icon', [$this, 'displayIcon'], ['is_safe' => ['html']]),
        ];
    }

    public function displayDatagrid(Datagrid $datagrid): ?string
    {
        return $this->twig->render(
            '@UnZeroUnDatagrid/datagrid.html.twig',
            ['datagrid' => $datagrid]
        );
    }

    public function displayRow(Datagrid $datagrid, $object): ?string
    {
        $realObject     = $object;
        $additionalData = [];
        if (is_array($object) && isset($object[0]) && is_object($object[0])) {
            $realObject     = $object[0];
            $additionalData = array_slice($object, 1);
        }

        return $this->twig->render(
            '@UnZeroUnDatagrid/datagrid/row.html.twig',
            [
                'datagrid'       => $datagrid,
                'object'         => $realObject,
                'additionalData' => $additionalData,
                'massActionForm' => $datagrid->getBatchActionFormView(),
            ]
        );
    }

    public function displayActionAttributes(array $attributes)
    {
        $htmlAttributes = [];

        foreach ($attributes as $attributeName => $attributeValue) {
            if (null === $attributeValue) {
                $htmlAttributes[] = $attributeName;
            } else {
                $htmlAttributes[] = sprintf(
                    '%s=%s',
                    $attributeName,
                    is_bool($attributeValue) ? ($attributeValue ? '1' : '0') : '"' . (string)$attributeValue . '"'
                );
            }
        }

        return implode(' ', $htmlAttributes);
    }

    public function displayCell(Datagrid $datagrid, $object, DatagridColumn $column): string
    {
        $value = $column->getAccessor()->getValue($object);

        $type = $column->getType();

        return $this->displayCellValue($datagrid, $object, $value, $type);
    }

    public function displayCellValue(Datagrid $datagrid, $object, $value, ?string $type = null)
    {
        if (null === $type) {
            $type = $this->guessColumnType($value);
        }

        $context  = ['datagrid' => $datagrid, 'object' => $object, 'value' => $value];
        $template = $this->twig->loadTemplate('@UnZeroUnDatagrid/datagrid/cells.html.twig');

        $customBlock = sprintf('datagrid_%s_cell', $type);

        if ($template->hasBlock($customBlock, $context)) {
            return $template->renderBlock($customBlock, $context);
        }

        return $template->renderBlock('datagrid_default_cell', $context);
    }

    public function displayHeader(Datagrid $datagrid)
    {
        if (!$datagrid->getGlobalActions()) {
            return '';
        }

        return $this->twig->render(
            '@UnZeroUnDatagrid/datagrid/header.html.twig',
            [
                'datagrid' => $datagrid,
                'actions'  => $datagrid->getGlobalActions(),
            ]
        );
    }

    public function displayIcon(ActionIcon $icon): string
    {
        $context  = ['icon' => $icon];
        $template = $this->twig->loadTemplate('@UnZeroUnDatagrid/datagrid/icons.html.twig');

        $blockName = sprintf('icon_%s', $icon->getType());

        if (!$template->hasBlock($blockName, $context)) {
            throw new \RuntimeException(
                sprintf('You have to define a block "%s" in template "%s"', $blockName, $template->getTemplateName())
            );
        }

        return $template->renderBlock($blockName, $context);
    }

    private function getFqcnSanitized(object $object): string
    {
        return $this->classNamingStrategy->classToTableName(get_class($object));
    }

    private function guessColumnType($value)
    {
        if (is_scalar($value)) {
            // todo identifier les liens et images selon une regex sur une chaine

            return gettype($value);
        }

        if (is_array($value)) {
            if (array_intersect(['fr', 'de', 'it', 'nl', 'en', 'es'], array_keys($value))) {
                return 'translated_string';
            }

            return 'array';
        }

        if (is_null($value)) {
            return 'null';
        }

        if (is_resource($value)) {
            return 'resource';
        }

        if ($value instanceof Collection) {
            return 'collection_count';
        }

        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            return 'datetime';
        }

        return $this->getFqcnSanitized($value);
    }
}
