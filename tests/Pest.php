<?php

declare(strict_types=1);

use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Semver\Constraint\Constraint;
use Phonyland\Framework\Phony;

function 🙃(): Phony
{
    return new Phony();
}

/**
 * Creates the generator requirement in the composer instance.
 *
 * @param  string         $packageName  the name of the generator package to fake
 * @param  string         $class        generator class to load
 * @param  string         $alias        the alias to use for the generator
 * @param  array<string>  $data         the data packages for the generator.
 * @param  bool           $dev          determines if it should be added as a dev dependency
 *
 * @throws \Throwable
 */
function fakeGenerator(
    string $packageName,
    string $class,
    string $alias,
    array $data = [],
    bool $dev = false
): void {
    $test = test();

    $requires = $dev
        ? $test->composer->getPackage()->getDevRequires()
        : $test->composer->getPackage()->getRequires();

    $link = new Link(
        'phonyland/generator-manager',
        $packageName,
        new Constraint('=', '9999999-dev'),
        'requires',
        'dev-master'
    );
    $requires[$packageName] = $link;

    if ($dev) {
        $test->composer->getPackage()->setDevRequires($requires);
    } else {
        $test->composer->getPackage()->setRequires($requires);
    }

    $repository = $test->composer->getRepositoryManager()->getLocalRepository();
    $package = new CompletePackage($packageName, '9999999-dev', 'dev-master');
    $package->setExtra([
        'phonyland' => [
            'generator' => [
                'class' => $class,
                'alias' => $alias,
                'data'  => $data,
            ],
        ],
    ]);
    $repository->addPackage($package);
}
