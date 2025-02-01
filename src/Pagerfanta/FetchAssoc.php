<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Pagerfanta;

use Aura\Sql\ExtendedPdoInterface;
use PDO;

final readonly class FetchAssoc implements FetcherInterface
{
    public function __construct(private ExtendedPdoInterface $pdo)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(string $sql, array $params): array
    {
        return $this->pdo->perform($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
