<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ExtendedPdoInterface;
use PDOException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Aop\ReflectionMethod;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\AuraSqlModule\Exception\RollbackException;

use function assert;
use function count;
use function is_array;

class TransactionalInterceptor implements MethodInterceptor
{
    public function __construct(private readonly ?ExtendedPdoInterface $pdo = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        $method = $invocation->getMethod();
        assert($method instanceof ReflectionMethod);
        $transactional = $method->getAnnotation(Transactional::class);
        assert($transactional instanceof Transactional);
        if (is_array($transactional->value) && count((array) $transactional->value) > 1) {
            return (new PropTransaction())($invocation, $transactional);
        }

        if (! $this->pdo instanceof ExtendedPdoInterface) {
            return $invocation->proceed();
        }

        try {
            $this->pdo->beginTransaction();
            $result = $invocation->proceed();
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            throw new RollbackException($e->getMessage(), 0, $e);
        }

        return $result;
    }
}
