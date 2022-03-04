<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Commands\DumpCommand;
use Phonyland\GeneratorManager\Container;
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

    fakeGenerator(
        packageName: 'phonyland/sample-one-generator',
        class: SampleOneGenerator::class,
        alias: 'sampleOne',
        data: [
            'tr' => 'phonyland/sample-one-generator-tr',
            'en' => 'phonyland/sample-one-generator-en',
            'de' => 'phonyland/sample-one-generator-de',
        ],
        dev: true
    );

    fakeGenerator(
        packageName: 'phonyland/sample-two-generator',
        class: SampleTwoGenerator::class,
        alias: 'sampleTwo',
        data: [
            'tr' => 'phonyland/sample-two-generator-tr',
            'en' => 'phonyland/sample-two-generator-en',
            'de' => 'phonyland/sample-two-generator-de',
        ],
        dev: true
    );

    fakeGenerator(
        packageName: 'phonyland/sample-three-generator',
        class: SampleThreeGenerator::class,
        alias: 'sampleThree',
        data: [
            'tr' => 'phonyland/sample-three-generator-tr',
            'en' => 'phonyland/sample-three-generator-en',
            'de' => 'phonyland/sample-three-generator-de',
        ],
        dev: true
    );

    $this->dump->run(new ArrayInput([]), new NullOutput());
});

it('exists')->assertTrue(class_exists(Container::class));

it('returns a single generator instance', function () {
    $container = new Container(🙃());

    $this->assertInstanceOf(SampleOneGenerator::class, $container->get('sampleOne'));
    $this->assertInstanceOf(SampleTwoGenerator::class, $container->get('sampleTwo'));
    $this->assertInstanceOf(SampleThreeGenerator::class, $container->get('sampleThree'));
});

it('throws runtime exception when generator cache file is missing', function () {
    $this->expectException(RuntimeException::class);

    unlink(getcwd().'/vendor/phonyland-generators.json');

    $container = new Container(🙃());

    $container->get('sampleOne');
});

it('returns no generators when generator cache file does not contain valid json', function () {
    $this->expectException(RuntimeException::class);

    file_put_contents(getcwd().'/vendor/phonyland-generators.json', 'abcd');

    $container = new Container(🙃());

    $container->get('sampleOne');
});
