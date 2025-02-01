<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector;
use ReflectionProperty;

use function assert;

class NamedPdoModuleTest extends TestCase
{
    public function testModule()
    {
        $qualifer = 'log_db';
        $instance = (new Injector(new NamedPdoModule($qualifer, 'sqlite::memory:'), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class, $qualifer);
        $this->assertInstanceOf(ExtendedPdo::class, $instance);
    }

    public function testFakeName()
    {
        $injector = new Injector(new FakeNamedModule(), __DIR__ . '/tmp');
        $fakeName = $injector->getInstance(FakeName::class);
        $this->assertInstanceOf(ExtendedPdo::class, $fakeName->pdo);
        $this->assertInstanceOf(ExtendedPdo::class, $fakeName->pdoAnno);
        $this->assertInstanceOf(ExtendedPdo::class, $fakeName->pdoSetterInject);
    }

    public function testReplicationMaster()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $qualifer = 'log_db';
        $instance = (new Injector(new FakeNamedReplicationModule(), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class, $qualifer);
        assert($instance instanceof ExtendedPdo);
        $this->assertInstanceOf(ExtendedPdo::class, $instance);
//        $this->assertSame('mysql:host=localhost;dbname=db', $instance->getDsn());
    }

    public function testReplicationSlave()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $qualifer = 'log_db';
        $instance = (new Injector(new FakeNamedReplicationModule(), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class, $qualifer);
        $this->assertInstanceOf(ExtendedPdo::class, $instance);
        $this->assertStringContainsString('mysql:host=slave', $this->getDsn($instance));
    }

    public function testNoHost()
    {
        $qualifer = 'log_db';
        $instance = (new Injector(new FakeNamedQualifierModule(), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class, $qualifer);
        /** @var ExtendedPdo $instance */
        $this->assertSame('mysql:host=slave1;dbname=master', $this->getDsn($instance));
    }

    private function getDsn(ExtendedPdo $pdo): string
    {
        $prop = new ReflectionProperty($pdo::class, 'args');
        $prop->setAccessible(true);
        $args = $prop->getValue($pdo);

        return $args[0]; // dsn
    }
}
