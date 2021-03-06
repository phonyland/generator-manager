<?php

declare(strict_types=1);

use Composer\Factory;
use Composer\IO\NullIO;
use Phonyland\GeneratorManager\Manager;
use Phonyland\GeneratorManager\PhonyCommandProvider;

beforeEach(function () {
    $this->manager = new Manager();
    $this->io = new NullIO();
    $this->composer = (new Factory())->createComposer($this->io);
});

it('exists')->assertTrue(class_exists(Manager::class));

it('removes the cached generators file on uninstall', function () {
    touch('vendor/phonyland-generators.json');

    $this->manager->uninstall($this->composer, $this->io);

    $this->assertFileDoesNotExist('vendor/phonyland-generators.json');
});

it('should create the cached generators file', function () {
    $this->manager->activate($this->composer, $this->io);
    $this->manager->registerGenerators();

    $this->assertFileExists('vendor/phonyland-generators.json');
});

it('subscribes for the post-autoload-dump event', function () {
    $this->assertArrayHasKey('post-autoload-dump', $this->manager->getSubscribedEvents());
});

it('has the capability for the dump command', function () {
    $this->assertContains(PhonyCommandProvider::class, $this->manager->getCapabilities());
});
