<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Pagerfanta;

use Aura\Sql\ExtendedPdoInterface;

class AuraSqlPagerFactory implements AuraSqlPagerFactoryInterface
{
    public function __construct(private readonly AuraSqlPagerInterface $auraSqlPager)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function newInstance(ExtendedPdoInterface $pdo, string $sql, array $params, int $paging, string $uriTemplate, ?string $entity = null): AuraSqlPagerInterface
    {
        $this->auraSqlPager->init($pdo, $sql, $params, $paging, new DefaultRouteGenerator($uriTemplate), $entity);

        return $this->auraSqlPager;
    }
}
