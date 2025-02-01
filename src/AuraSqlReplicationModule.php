<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ConnectionLocatorInterface;
use Aura\Sql\ExtendedPdoInterface;
use Ray\AuraSqlModule\Annotation\ReadOnlyConnection;
use Ray\AuraSqlModule\Annotation\WriteConnection;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class AuraSqlReplicationModule extends AbstractModule
{
    public function __construct(
        private readonly ConnectionLocatorInterface $connectionLocator,
        private readonly string $qualifer = '',
        ?AbstractModule $module = null
    ) {
        parent::__construct($module);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->bind(ConnectionLocatorInterface::class)
            ->annotatedWith($this->qualifer)
            ->toInstance($this->connectionLocator);

        // ReadOnlyConnection when GET, otherwise WriteConnection
        $this->bind(ExtendedPdoInterface::class)
            ->annotatedWith($this->qualifer)
            ->toProvider(AuraSqlReplicationDbProvider::class, $this->qualifer)
            ->in(Scope::SINGLETON);

        // @ReadOnlyConnection @WriteConnection
        $this->installReadWriteConnection();
    }

    protected function installReadWriteConnection(): void
    {
        // @ReadOnlyConnection
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(ReadOnlyConnection::class),
            [AuraSqlSlaveDbInterceptor::class],
        );
        // @WriteConnection
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(WriteConnection::class),
            [AuraSqlMasterDbInterceptor::class],
        );
    }
}
