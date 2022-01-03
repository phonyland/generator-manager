<?php

use Phonyland\GeneratorManager\Loader;
use Phonyland\GeneratorManager\Tests\Stubs\AnotherDummyInterface;
use Phonyland\GeneratorManager\Tests\Stubs\DummyInterface;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorOne;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorTwo;
use Phonyland\GeneratorManager\Tests\Stubs\GeneratorThree;

beforeEach(function () {
    file_put_contents(
        sprintf('%s/vendor/phonyland-generators.json', getcwd()),
        json_encode([
            GeneratorOne::class,
            GeneratorTwo::class,
            GeneratorThree::class,
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

it('return no generators when generator cache file is missing', function () {
    unlink(sprintf('%s/vendor/phonyland-generators.json', getcwd()));
    $generators = Loader::getGenerators(DummyInterface::class);

    $this->assertEmpty($generators);
});

it('returns no generators when generator cache file does not contain valid json', function () {
    file_put_contents(sprintf('%s/vendor/phonyland-generators.json', getcwd()), 'abcd');
    $generators = Loader::getGenerators(DummyInterface::class);

    $this->assertEmpty($generators);
});
