<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager\Commands;
/**
 * @internal
 */
final class DumpCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('phonyland:dump-generators');
    }

}