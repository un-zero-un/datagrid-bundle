<?php

declare(strict_types=1);

namespace UnZeroUn\Datagrid\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UnZeroUn\Datagrid\Action\Route\Parameters\RouteParameters;
use UnZeroUn\Datagrid\Action\Url\AbsoluteUrl;
use UnZeroUn\Datagrid\Action\Url\RelativeUrl;
use UnZeroUn\Datagrid\Action\Url\RouteUrl;

class ActionUrlBuilder
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Request
     */
    private $request;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->request      = $requestStack->getCurrentRequest();
    }

    public function currentUrl(): RelativeUrl
    {
        return new RelativeUrl($this->request->getRequestUri());
    }

    public function absoluteUrl(string $url): AbsoluteUrl
    {
        return new AbsoluteUrl($url);
    }

    public function routeUrl(string $routeName, $routeParameters = []): RouteUrl
    {
        return new RouteUrl($this->urlGenerator, $routeName, $routeParameters);
    }
}
