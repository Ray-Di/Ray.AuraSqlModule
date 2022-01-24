<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule;

use Aura\SqlQuery\Common\DeleteInterface;
use Aura\SqlQuery\QueryFactory;
use Ray\AuraSqlModule\Annotation\AuraSqlQueryConfig;
use Ray\Di\ProviderInterface;

class AuraSqlQueryDeleteProvider implements ProviderInterface
{
    private string $db;

    /**
     * @param string $db The database type
     *
     * @AuraSqlQueryConfig
     */
    #[AuraSqlQueryConfig]
    public function __construct(string $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     *
     * @return DeleteInterface
     */
    public function get()
    {
        return (new QueryFactory($this->db))->newDelete();
    }
}
