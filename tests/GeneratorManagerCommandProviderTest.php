<?php

declare(strict_types=1);

use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\GeneratorManagerCommandProvider;

it('exists')->assertTrue(class_exists(GeneratorManagerCommandProvider::class));

it('returns the dump command', function () {
    $commandProvider = new GeneratorManagerCommandProvider();
    $commands = $commandProvider->getCommands();

    $this->assertCount(1, $commands);
    $this->assertInstanceOf(DumpCommand::class, $commands[0]);
});
