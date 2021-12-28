<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Composer\Plugin\Capability\CommandProvider;
use Phonyland\GeneratorManager\Commands\DumpCommand;

/**
 * @internal
 */
final class PhonyCommandProvider implements CommandProvider
{
    /**
     * @return array<int, DumpCommand>
     */
    public function getCommands(): array
    {
        return [new DumpCommand()];
    }
}
