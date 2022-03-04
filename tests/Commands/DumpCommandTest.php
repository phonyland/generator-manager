<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\Tests\Stubs\SampleOneGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleThreeGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleTwoGenerator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

beforeEach(function () {
    $this->io = new NullIO();
    $this->composer = (new Factory())->createComposer($this->io);
    $this->dump = new DumpCommand();
    $this->dump->setComposer($this->composer);
});

it('exists')->assertTrue(class_exists(DumpCommand::class));

it('should find a single generator with the generator class', function () {
    fakeGenerator('phonyland/sample-one-generator', SampleOneGenerator::class, 'sampleOne');

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertEquals(SampleOneGenerator::class, $generators['sampleOne']['class']);
    $this->assertEquals('sampleOne', $generators['sampleOne']['alias']);
    $this->assertEquals([], $generators['sampleOne']['data']);
});

it('should find multiple generators', function () {
    fakeGenerator('phonyland/sample-one-generator', SampleOneGenerator::class, 'sampleOne');
    fakeGenerator('phonyland/sample-two-generator', SampleTwoGenerator::class, 'sampleTwo');

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertEquals(SampleOneGenerator::class, $generators['sampleOne']['class']);
    $this->assertEquals('sampleOne', $generators['sampleOne']['alias']);
    $this->assertEquals([], $generators['sampleOne']['data']);

    $this->assertEquals(SampleTwoGenerator::class, $generators['sampleTwo']['class']);
    $this->assertEquals('sampleTwo', $generators['sampleTwo']['alias']);
    $this->assertEquals([], $generators['sampleTwo']['data']);
});

it('should find a dev generator', function () {
    fakeGenerator(
        packageName: 'phonyland/sample-one-generator',
        class: SampleOneGenerator::class,
        alias: 'sampleOne',
        dev: true
    );

    $this->dump->run(new ArrayInput([]), new NullOutput());

    $generators = json_decode(
        json: file_get_contents('vendor/phonyland-generators.json'),
        associative: true,
        depth: 1024,
        flags: JSON_THROW_ON_ERROR
    );

    $this->assertEquals(SampleOneGenerator::class, $generators['sampleOne']['class']);
});

it('should find a generator during development', function () {
    $composer = test()->composer;
    $extra = $composer->getPackage()->getExtra();

    $extra['phonyland'] = [
        'generator' => [
            'class' => SampleThreeGenerator::class,
            'alias' => 'sampleThree',
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

    $this->assertEquals(SampleThreeGenerator::class, $generators['sampleThree']['class']);
    $this->assertEquals('sampleThree', $generators['sampleThree']['alias']);
    $this->assertEquals([], $generators['sampleThree']['data']);
});
