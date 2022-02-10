<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Phonyland\Framework\Phony;

abstract class Generator
{
    public function __construct(
        protected Phony $phony
    ) {
    }
}
