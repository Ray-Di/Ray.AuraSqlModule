<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\ExtendedPdoInterface;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector;

use function putenv;

class AuraSqEnvlModuleTest extends TestCase
{
    public function setUp(): void
    {
        putenv('TEST_DSN=sqlite::memory:');
        putenv('TEST_USER=user1');
        putenv('TEST_PASSWORD=password1');
    }

    public function tearDown(): void
    {
        putenv('TEST_DSN');
        putenv('TEST_USER');
        putenv('TEST_PASSWORD');
    }

    public function testModule()
    {
        $module = new AuraSqlEnvModule('TEST_DSN', 'TEST_USER', 'TEST_PASSWORD');
        $injector = new Injector($module, __DIR__ . '/tmp');
        $instance = $injector->getInstance(ExtendedPdoInterface::class);
        $this->assertInstanceOf(ExtendedPdo::class, $instance);
        $this->assertSame('user1', $injector->getInstance('', 'pdo_user'));
        $this->assertSame('password1', $injector->getInstance('', 'pdo_pass'));
    }
}