<?php

declare(strict_types=1);

namespace Phonyland\GeneratorManager;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Manager implements PluginInterface
{
    public const GENERATOR_CACHE_FILE = 'phonyland-generators.json';

    private Composer $composer;
    private IOInterface $io;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    /** {@inheritdoc} */
    public function deactivate(Composer $composer, IOInterface $io): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $vendorDirectory = $composer->getConfig()->get('vendor-dir');
        $pluginFile      = sprintf('%s/%s', $vendorDirectory, self::GENERATOR_CACHE_FILE);

        if (file_exists($pluginFile)) {
            unlink($pluginFile);
        }
    }
}