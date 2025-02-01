<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ConnectionLocator;
use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\AbstractModule;

class NamedPdoModule extends AbstractModule
{
    public const PARSE_PDO_DSN_REGEX = '/(.*?)\:(host|server)=.*?;(.*)/i';

    /**
     * @param string        $qualifer Qualifer for ExtendedPdoInterface
     * @param string        $dsn      Data Source Name (DSN)
     * @param string        $username User name for the DSN string
     * @param string        $password Password for the DSN string
     * @param string        $slave    Comma separated slave host list
     * @param array<string> $options  A key=>value array of driver-specific connection options
     * @param array<string> $queries  Queries to execute after the connection.
     */
    public function __construct(
        private readonly string $qualifer,
        private readonly string $dsn,
        private readonly string $username = '',
        private readonly string $password = '',
        private readonly string $slave = '',
        private readonly array $options = [],
        private readonly array $queries = []
    ) {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->slave ? $this->configureMasterSlaveDsn()
            : $this->configureSingleDsn();
    }

    private function configureSingleDsn(): void
    {
        $this->bind(ExtendedPdoInterface::class)
            ->annotatedWith($this->qualifer)
            ->toConstructor(
                ExtendedPdo::class,
                "dsn={$this->qualifer}_dsn,username={$this->qualifer}_username,password={$this->qualifer}_password",
            );
        $this->bind()->annotatedWith("{$this->qualifer}_dsn")->toInstance($this->dsn);
        $this->bind()->annotatedWith("{$this->qualifer}_username")->toInstance($this->username);
        $this->bind()->annotatedWith("{$this->qualifer}_password")->toInstance($this->password);
    }

    private function configureMasterSlaveDsn(): void
    {
        $locator = $this->getLocator();
        $this->install(new AuraSqlReplicationModule($locator, $this->qualifer));
    }

    private function getLocator(): ConnectionLocator
    {
        return ConnectionLocatorFactory::fromInstance(
            $this->dsn,
            $this->username,
            $this->password,
            $this->slave,
            $this->options,
            $this->queries,
        );
    }
}
