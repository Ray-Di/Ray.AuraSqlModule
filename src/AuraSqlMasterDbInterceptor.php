<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ConnectionLocatorInterface;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use ReflectionProperty;

class AuraSqlMasterDbInterceptor implements MethodInterceptor
{
    public const PROP = 'pdo';

    public function __construct(private readonly ConnectionLocatorInterface $connectionLocator)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        $object = $invocation->getThis();
        $ref = new ReflectionProperty($object, self::PROP);
        $ref->setAccessible(true);
        $connection = $this->connectionLocator->getWrite();
        $ref->setValue($object, $connection);

        return $invocation->proceed();
    }
}
