<?php

declare(strict_types=1);

namespace Ray\AuraSqlModule\Annotation;

use Attribute;
use Ray\Di\Di\Qualifier;

/**
 * @Annotation
 * @Target("METHOD")
 * @Qualifier
 * @deprecated -- No one using?
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::TARGET_METHOD), Qualifier]
final class AuraSqlConfig
{
    /** @var ?array<string> */
    public $value;

    /**
     * @param array<string> $value
     */
    public function __construct(?array $value = null)
    {
        $this->value = $value;
    }
}
