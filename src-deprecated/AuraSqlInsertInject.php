<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\SqlQuery\Common\InsertInterface;
use Ray\Di\Di\Inject;

/** @deprecated Use constructor injection instead */
trait AuraSqlInsertInject
{
    /** @var InsertInterface */
    protected $insert;

    /** @Inject */
    #[Inject]
    public function setAuraSqlInsert(InsertInterface $insert): void
    {
        $this->insert = $insert;
    }
}
