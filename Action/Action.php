<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action;

use Symfony\Component\HttpFoundation\ParameterBag;
use UnZeroUn\Datagrid\Action\Url\Url;

class Action
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var ParameterBag
     */
    private $classes;

    /**
     * @var ParameterBag
     */
    private $attributes;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var Url
     */
    private $url;

    public function __construct(string $label, Url $url)
    {
        $this->label      = $label;
        $this->url        = $url;
        $this->classes    = [];
        $this->attributes = new ParameterBag();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(...$args): string
    {
        return $this->url->getUrl(...$args);
    }

    public function addClass(string $class): self
    {
        $this->classes = array_unique(array_merge($this->classes, [$class]));

        return $this;
    }

    public function removeClass(string $class): self
    {
        $this->classes = array_filter(
            $this->classes,
            function (string $existingClass) use ($class) {
                return $existingClass !== $class;
            }
        );

        return $this;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function addAttribute(string $attributeName, $attributeValue): self
    {
        $this->attributes->set($attributeName, $attributeValue);

        return $this;
    }

    public function removeAttribute(string $attributeName): self
    {
        $this->attributes->remove($attributeName);

        return $this;
    }

    public function getAttributes(): ParameterBag
    {
        return $this->attributes;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function btn(string $type, ?string $size = null): self
    {
        $this->addClass('btn')
            ->addClass('btn-' . $type);

        if (null !== $size) {
            $this->addClass('btn-'.$size);
        }

        return $this;
    }

    public function btnOutline(string $type, ?string $size = null): self
    {
        $this->btn('outline-'.$type, $size);

        return $this;
    }
}
