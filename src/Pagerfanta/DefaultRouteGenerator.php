<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Pagerfanta;

class DefaultRouteGenerator implements RouteGeneratorInterface
{
    public function __construct(private readonly string $uri)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($page)
    {
        return uri_template($this->uri, ['page' => $page]);
    }
}
