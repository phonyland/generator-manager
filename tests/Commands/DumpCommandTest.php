<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorOne;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorTwo;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorThree;
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
    fakeGenerator('phonyland/generator-one', [GeneratorOne::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(GeneratorOne::class, $generators);
});

it('should find a single generator with multiple generator classes', function () {
    fakeGenerator('phonyland/generator-one', [GeneratorOne::class, GeneratorTwo::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(GeneratorOne::class, $generators);
    $this->assertContains(GeneratorTwo::class, $generators);
});

it('should find multiple generators', function () {
    fakeGenerator('phonyland/generator-one', [GeneratorOne::class]);
    fakeGenerator('phonyland/generator-two', [GeneratorTwo::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(GeneratorOne::class, $generators);
    $this->assertContains(GeneratorTwo::class, $generators);
});

it('should find a dev generator', function () {
    fakeGenerator(
        generatorName: 'phonyland/generator-one',
        classes: [GeneratorOne::class],
        dev: true
    );

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(GeneratorOne::class, $generators);
});

it('should find a generator during development', function () {
    $composer = test()->composer;
    $extra = $composer->getPackage()->getExtra();

    $extra['phonyland'] = [
        'generators' => [
            GeneratorThree::class,
        ],
    ];

    $composer->getPackage()->setExtra($extra);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(GeneratorThree::class, $generators);
});
