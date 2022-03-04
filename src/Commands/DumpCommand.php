<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager\Commands;

use Composer\Command\BaseCommand;
use Phonyland\GeneratorManager\Manager;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class DumpCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('phonyland:reload-generators');
    }

    /**
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = $this->getComposer();

        if ($composer === null) {
            throw new RuntimeException('Could not get Composer\Composer instance.');
        }

        $vendorDirectory = $composer->getConfig()->get('vendor-dir');
        $generators = [];

        $packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
        $packages[] = $composer->getPackage();

        /** @var \Composer\Package\PackageInterface $package */
        foreach ($packages as $package) {
            if (! isset($package->getExtra()['phonyland']['generator'])) {
                continue;
            }

            $extra = $package->getExtra()['phonyland']['generator'];

            $generators[$extra['alias']] = [
                'class' => $extra['class'],
                'alias' => $extra['alias'],
                'data'  => $extra['data'] ?? [],
            ];
        }

        file_put_contents(
            filename: implode(DIRECTORY_SEPARATOR, [$vendorDirectory, Manager::GENERATOR_CACHE_FILE]),
            data: json_encode($generators, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
        );

        return 0;
    }
}
