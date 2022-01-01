<?php

use Phonyland\GeneratorManager\Loader;
use Phonyland\GeneratorManager\Tests\Stubs\AnotherDummyInterface;
use Phonyland\GeneratorManager\Tests\Stubs\DummyInterface;
use Phonyland\GeneratorManager\Tests\Stubs\Generator1;
use Phonyland\GeneratorManager\Tests\Stubs\Generator2;
use Phonyland\GeneratorManager\Tests\Stubs\Generator3;

beforeEach(function () {
    file_put_contents(
        sprintf('%s/vendor/phonyland-generators.json', getcwd()),
        json_encode([
            Generator1::class,
            Generator2::class,
            Generator3::class,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
    );
});

afterEach(function () {
    Loader::reset();
});

it('exists')->assertTrue(class_exists(Loader::class));

it('returns a single generator instance', function () {
    $generators = Loader::getGenerators(DummyInterface::class);

    $this->assertCount(1, $generators);
    $this->assertInstanceOf(DummyInterface::class, $generators[0]);
});

it('returns multiple generator instances', function () {
    $generators = Loader::getGenerators(AnotherDummyInterface::class);

    $this->assertCount(2, $generators);
    $this->assertInstanceOf(AnotherDummyInterface::class, $generators[0]);
    $this->assertInstanceOf(AnotherDummyInterface::class, $generators[1]);
});

it('return no generators when plugin cache file is missing', function () {
    unlink(sprintf('%s/vendor/phonyland-generators.json', getcwd()));
    $generators = Loader::getGenerators(DummyInterface::class);

    $this->assertEmpty($generators);
});