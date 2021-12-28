<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\Tests\Stubs\Generator1;
use Phonyland\GeneratorManager\Tests\Stubs\Generator2;
use Phonyland\GeneratorManager\Tests\Stubs\Generator3;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

beforeEach(function () {
    $this->io = new NullIO();
    $this->composer = (new Factory())->createComposer($this->io);
    $this->dump = new DumpCommand();
    $this->dump->setComposer($this->composer);
});

it('exists')->assertTrue(class_exists(DumpCommand::class));

it('should find a single generator with one generator class', function () {
    fakeGenerator('phonyland/generator1', [Generator1::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(Generator1::class, $generators);
});
