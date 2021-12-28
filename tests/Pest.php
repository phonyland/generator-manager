<?php

declare(strict_types=1);

use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Semver\Constraint\Constraint;

/**
 * Creates the generator requirement in the composer instance.
 *
 * @param  string              $generatorName  the name of the generator to fake
 * @param  array<int, string>  $classes        generator classes to load
 * @param  bool                $dev            determines if it should be added as a dev dependency
 *
 * @throws \Throwable
 */
function fakeGenerator(string $generatorName, array $classes, bool $dev = false): void
{
    $test = test();

    $requires = $dev
        ? $test->composer->getPackage()->getDevRequires()
        : $test->composer->getPackage()->getRequires();

    $link = new Link(
        'phonyland/generator-manager',
        $generatorName,
        new Constraint('=', '9999999-dev'),
        'requires',
        'dev-master'
    );
    $requires[$generatorName] = $link;

    if ($dev) {
        $test->composer->getPackage()->setDevRequires($requires);
    } else {
        $test->composer->getPackage()->setRequires($requires);
    }

    $repository = $test->composer->getRepositoryManager()->getLocalRepository();
    $package = new CompletePackage($generatorName, '9999999-dev', 'dev-master');
    $package->setExtra([
        'phonyland' => [
            'generators' => $classes,
        ],
    ]);
    $repository->addPackage($package);
}
