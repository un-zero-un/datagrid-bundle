<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action;

use UnZeroUn\Datagrid\Action\Url\Url;

class Action
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var ActionIcon|null
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
        $this->attributes = [];
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(array $context = []): string
    {
        return $this->url->getUrl($context);
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
        $this->attributes[$attributeName] = $attributeValue;

        return $this;
    }

    public function removeAttribute(string $attributeName): self
    {
        if (isset($this->attributes[$attributeName])) {
            unset($this->attributes[$attributeName]);
        }

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setIcon(ActionIcon $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?ActionIcon
    {
        return $this->icon;
    }
}
