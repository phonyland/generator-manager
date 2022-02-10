<?php

use Phonyland\GeneratorManager\Container;
use Phonyland\GeneratorManager\Tests\Stubs\SampleOneGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleThreeGenerator;
use Phonyland\GeneratorManager\Tests\Stubs\SampleTwoGenerator;

beforeEach(function () {
    file_put_contents(
        filename: getcwd().'/vendor/phonyland-generators.json',
        data: json_encode([
            'sampleOne'   => SampleOneGenerator::class,
            'sampleTwo'   => SampleTwoGenerator::class,
            'sampleThree' => SampleThreeGenerator::class,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
    );
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
