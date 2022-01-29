<?php

use Phonyland\GeneratorManager\Loader;
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

afterEach(function () {
    Loader::reset();
});

it('exists')->assertTrue(class_exists(Loader::class));

it('returns a single generator instance', function () {
    $generators = Loader::getGenerators();

    $this->assertCount(3, $generators);
    $this->assertInstanceOf(SampleOneGenerator::class, $generators['sampleOne']);
    $this->assertInstanceOf(SampleTwoGenerator::class, $generators['sampleTwo']);
    $this->assertInstanceOf(SampleThreeGenerator::class, $generators['sampleThree']);
});

it('return no generators when generator cache file is missing', function () {
    unlink(getcwd().'/vendor/phonyland-generators.json');
    $generators = Loader::getGenerators();

    $this->assertEmpty($generators);
});

it('returns no generators when generator cache file does not contain valid json', function () {
    file_put_contents(getcwd().'/vendor/phonyland-generators.json', 'abcd');
    $generators = Loader::getGenerators();

    $this->assertEmpty($generators);
});
