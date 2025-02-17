<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ExtendedPdo;

class Connection
{
    private ?ExtendedPdo $pdo = null;

    /**
     * @phpstan-param array<string> $options
     * @phpstan-param array<string> $queries
     */
    public function __construct(
        private readonly string $dsn,
        private readonly string $username = '',
        private readonly string $password = '',
        /** @var array<string> */
        private readonly array $options = [],
        /** @var array<string> */
        private readonly array $queries = []
    ) {
    }

    public function __invoke(): ExtendedPdo
    {
        if ($this->pdo instanceof ExtendedPdo) {
            return $this->pdo;
        }

        $this->pdo = new ExtendedPdo($this->dsn, $this->username, $this->password, $this->options, $this->queries);

        return $this->pdo;
    }
}
