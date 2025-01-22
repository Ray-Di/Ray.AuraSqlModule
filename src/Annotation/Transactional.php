<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Transactional
{
    /**
     * @var ?array<string>
     * @deprecated
     */
    public $value;

    /**
     * @param array<string> $value
     */
    public function __construct(array $value = ['pdo'])
    {
        $this->value = $value;
    }
}
