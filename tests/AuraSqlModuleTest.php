<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ConnectionLocatorInterface;
use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PHPUnit\Framework\TestCase;
use Ray\Compiler\DiCompiler;
use Ray\Compiler\ScriptInjector;
use Ray\Di\Injector;
use Ray\Di\Instance;
use ReflectionProperty;

use function assert;
use function get_class;

class AuraSqlModuleTest extends TestCase
{
    public function testModule()
    {
        $instance = (new Injector(new AuraSqlModule('sqlite::memory:'), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class);
        $this->assertInstanceOf(ExtendedPdo::class, $instance);
    }

    public function testCompile()
    {
        (new DiCompiler(new AuraSqlModule('sqlite::memory:'), __DIR__ . '/tmp'))->compile();
        $pdo = (new ScriptInjector(__DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class);
        $this->assertInstanceOf(ExtendedPdoInterface::class, $pdo);
    }

    public function testMysql()
    {
        $fakeInject = (new Injector(new AuraSqlModule('mysql:host=localhost;dbname=master'), __DIR__ . '/tmp'))->getInstance(FakeQueryInject::class);
        [$db] = $fakeInject->get();
        $this->assertSame('mysql', $db);
    }

    public function testPgsql()
    {
        $fakeInject = (new Injector(new AuraSqlModule('pgsql:host=localhost;dbname=master'), __DIR__ . '/tmp'))->getInstance(FakeQueryInject::class);
        [$db] = $fakeInject->get();
        $this->assertSame('pgsql', $db);
    }

    public function testSqlite()
    {
        $fakeInject = (new Injector(new AuraSqlModule('sqlite:memory:'), __DIR__ . '/tmp'))->getInstance(FakeQueryInject::class);
        [$db] = $fakeInject->get();
        $this->assertSame('sqlite', $db);
    }

    public function testSlaveModule()
    {
        $module = new AuraSqlModule('mysql:host=localhost;dbname=testdb', 'root', '', 'slave1,slave2');
        $instance = $module->getContainer()->getContainer()['Aura\Sql\ConnectionLocatorInterface-'];
        assert($instance instanceof Instance);
        $locator = $instance->value;
        assert($locator instanceof ConnectionLocatorInterface);
        $this->assertInstanceOf(ConnectionLocatorInterface::class, $locator);
        $dsn = $this->getDsn($locator->getRead());
        $this->assertContains($dsn, ['mysql:host=slave1;dbname=testdb', 'mysql:host=slave2;dbname=testdb']);
    }

    public function testNoHost()
    {
        $instance = (new Injector(new FakeQualifierModule(), __DIR__ . '/tmp'))->getInstance(ExtendedPdoInterface::class);
        /** @var ExtendedPdo $instance */
        $this->assertSame('sqlite::memory:', $this->getDsn($instance));
    }

    private function getDsn(ExtendedPdo $pdo): string
    {
        $prop = new ReflectionProperty(get_class($pdo), 'args');
        $prop->setAccessible(true);
        $args = $prop->getValue($pdo);

        return $args[0]; // dsn
    }
}
