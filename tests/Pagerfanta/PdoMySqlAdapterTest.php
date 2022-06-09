<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Pagerfanta;

class PdoMySqlAdapterTest extends AbstractPdoTestCase
{
    private ExtendedPdoAdapter $adapter;

    public function setUp(): void
    {
        parent::setUp();
        $sql = 'SELECT * FROM posts';
        $this->adapter = new ExtendedPdoAdapter($this->pdo, $sql, []);
    }

    public function testGetNbResults(): void
    {
        $this->assertSame(50, $this->adapter->getNbResults());
    }

    public function testGetResults(): void
    {
        $expected = [
            [
                'id' => '3',
                'username' => 'BEAR',
                'post_content' => 'entry #3',
            ],
            [
                'id' => '4',
                'username' => 'BEAR',
                'post_content' => 'entry #4',
            ],
        ];
        $this->assertSame($expected, $this->adapter->getSlice(2, 2));
    }
}
