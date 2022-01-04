<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\Tests\Stubs\SampleOneGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleTwoGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleThreeGenerator;
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
    fakeGenerator('phonyland/sample-one-generator', [SampleOneGenerator::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertContains(SampleOneGenerator::class, $generators);
});

it('should find a single generator with multiple generator classes', function () {
    fakeGenerator('phonyland/sample-one-generator', [SampleOneGenerator::class, SampleTwoGenerator::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    expect($generators)->toMatchArray(['sample_one' => SampleOneGenerator::class]);
    expect($generators)->toMatchArray(['sample_two' => SampleTwoGenerator::class]);
});

it('should find multiple generators', function () {
    fakeGenerator('phonyland/sample-one-generator', [SampleOneGenerator::class]);
    fakeGenerator('phonyland/sample-two-generator', [SampleTwoGenerator::class]);

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    expect($generators)->toMatchArray(['sample_one' => SampleOneGenerator::class]);
    expect($generators)->toMatchArray(['sample_two' => SampleTwoGenerator::class]);
});

it('should find a dev generator', function () {
    fakeGenerator(
        generatorName: 'phonyland/sample-one-generator',
        classes: [SampleOneGenerator::class],
        dev: true
    );

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    expect($generators)->toMatchArray(['sample_one' => SampleOneGenerator::class]);
});

it('should find a generator during development', function () {
    $composer = test()->composer;
    $extra = $composer->getPackage()->getExtra();

    $extra['phonyland'] = [
        'generators' => [
            SampleThreeGenerator::class,
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

    expect($generators)->toMatchArray(['sample_three' => SampleThreeGenerator::class]);
});
