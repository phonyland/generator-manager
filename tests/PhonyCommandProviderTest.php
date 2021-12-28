<?php

declare(strict_types=1);

use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\PhonyCommandProvider;

it('exists')->assertTrue(class_exists(PhonyCommandProvider::class));

it('returns the dump command', function () {
    $commandProvider = new PhonyCommandProvider();
    $commands = $commandProvider->getCommands();

    $this->assertCount(1, $commands);
    $this->assertInstanceOf(DumpCommand::class, $commands[0]);
});
