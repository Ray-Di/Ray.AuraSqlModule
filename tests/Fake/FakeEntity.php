<?php

namespace Ray\AuraSqlModule;

final class FakeEntity
{
    public function __construct(public string $id, public string $name, public string $post_content)
    {
    }
}
